<?php


namespace App\Cabinet\Services\Payment\Payers\Qiwi;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Services\Payment\Payers\Payer as ContractPayer;
use App\Core\Application;
use App\Core\Entity\User;
use App\Core\Exceptions\Exception;
use App\Core\Http\Request;
use Qiwi\Api\BillPayments;

class Payer extends ContractPayer
{
    /**
     * @var BillPayments
     */
    protected $api;

    /**
     * @var PaymentHistory
     */
    private $payment;

    /**
     * Payer constructor.
     *
     * @param array $config
     * @throws \ErrorException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->api = new BillPayments($this->getSecretKey());
    }

    /**
     * @inheritDoc
     */
    public function init(Request $request)
    {
        parent::init($request);
        $bill = $request->post('bill');
        if (is_array($bill)) {
            $this->payment = $this->getPaymentHistoryModel()->find((int) $bill['billId']);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'qiwi';
    }

    /**
     * @inheritDoc
     */
    public function paymentUrl(User $user, int $cost): string
    {
        $payment = PaymentHistory::createEntity($user, $this->name(), $cost, false);
        $this->getPaymentHistoryModel()->insert($payment);

        $data = [
            'publicKey' => $this->getPublicKey(),
            'amount' => $cost,
            'billId' => $payment->id,
            'comment' => str_replace('%name%', $user->getName(), $this->getComment()),
        ];
        if (!empty($this->getSuccessUrl())) {
            $data['successUrl'] = $this->getSuccessUrl();
        }
        if (!empty($this->getTheme())) {
            $data['customFields'] = [
                'themeCode' => $this->getTheme()
            ];
        }
        return $this->api->createPaymentForm($data);
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): int
    {
        return $this->payment->user_id;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        return (int) $this->request->post('bill', [])['amount']['value'];
    }

    /**
     * @inheritDoc
     */
    public function validate(): bool
    {
        if (is_null($this->payment) || !is_null($this->payment->completed_at)) {
            throw new Exception('Счет с таким ID не найден или уже оплачен!');
        }

        $bill = $this->request->post('bill', []);
        $signature = $this->request->header('HTTP_X_API_SIGNATURE_SHA256', '');
        if (
            empty($bill)
            || !is_array($bill)
            || empty($signature)
            || !$this->api->checkNotificationSignature($signature, compact('bill'), $this->getSecretKey())
        ) return false;

        if ($bill['status']['value'] !== 'PAID') {
            throw new Exception('Неверный статус платежа');
        }

        if ($bill['amount']['currency'] !== 'RUB') {
            throw new Exception('Принимаются только рублевые платежи!');
        }

        if ($this->getSum() < 1) {
            throw new Exception('Сумма должна быть больше 1!');
        }

        if ($this->payment->amount < $this->getSum()) {
            throw new Exception('Сумма меньше заявленной!');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function successResponse(string $message): string
    {
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function errorResponse(string $message): string
    {
        return $message;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->config['public_key'] ?? '';
    }

    /**
     * @param string $key
     */
    public function setPublicKey(string $key): void
    {
        $this->config['public_key'] = $key;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->config['secret_key'] ?? '';
    }

    /**
     * @param string $key
     */
    public function setSecretKey(string $key): void
    {
        $this->config['secret_key'] = $key;
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->config['theme'] ?? '';
    }

    /**
     * @param string $theme
     */
    public function setTheme(string $theme): void
    {
        $this->config['theme'] = $theme;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->config['comment'] ?? 'Пополнение счета игрока %name%';
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->config['comment'] = $comment;
    }

    /**
     * @return string
     */
    public function getSuccessUrl(): string
    {
        return trim($this->config['success_url'] ?? rtrim(Application::getInstance()->getDLEConfig()->getHost(), '/') . base_url('cabinet'));
    }

    /**
     * @param string $url
     */
    public function setSuccessUrl(string $url): void
    {
        $this->config['success_url'] = $url;
    }

    /**
     * @inheritDoc
     */
    public function complete(User $user): string
    {
        $this->getPaymentHistoryModel()->complete($this->payment);
        return 'OK';
    }
}
