<?php


namespace App\Cabinet\Services\Payment\Payers\Freekassa;


use App\Cabinet\Services\Payment\Payers\Payer as ContractPayer;
use App\Core\Entity\User;
use App\Core\Exceptions\Exception;

class Payer extends ContractPayer
{
    private const URL = 'https://www.free-kassa.ru/merchant/cash.php';

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'freekassa';
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->config['id'] ?? 0;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->config['id'] = $id;
    }

    /**
     * @return string
     */
    public function getSecretKey1(): string
    {
        return $this->config['secret1'] ?? '';
    }

    /**
     * @param string $key
     */
    public function setSecretKey1(string $key): void
    {
        $this->config['secret1'] = $key;
    }

    /**
     * @return string
     */
    public function getSecretKey2(): string
    {
        return $this->config['secret2'] ?? '';
    }

    /**
     * @param string $key
     */
    public function setSecretKey2(string $key): void
    {
        $this->config['secret2'] = $key;
    }

    /**
     * @inheritDoc
     */
    public function paymentUrl(User $user, int $cost): string
    {
        $payment = new Payment($user->name, $cost);

        return static::URL . '?' . http_build_query($this->createUrlParams($payment));
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->request->any('MERCHANT_ORDER_ID');
    }

    /**
     * @inheritDoc
     */
    public function getSum(): int
    {
        return (int) $this->request->any('AMOUNT');
    }

    /**
     * @inheritDoc
     */
    public function validate(): bool
    {
        $data = $this->request->any();
        $shopId = (int) ($data['MERCHANT_ID'] ?? 0);
        if ($shopId != $this->getId()) {
            throw new Exception('Неправильный ID магазина!');
        }

        if ((int) ($data['AMOUNT'] ?? 0) < 1) {
            throw new Exception('Сумма должна быть больше 0!');
        }

        $sign = $data['SIGN'] ?? null;

        if (empty($sign)) {
            return false;
        }

        return $sign === md5($data['MERCHANT_ID'].':'.$data['AMOUNT'].':' . $this->getSecretKey2() . ':'.$data['MERCHANT_ORDER_ID']);
    }

    /**
     * @inheritDoc
     */
    public function successResponse(string $message): string
    {
        return 'YES';
    }

    /**
     * @inheritDoc
     */
    public function errorResponse(string $message): string
    {
        return $message;
    }

    /**
     * @param Payment $payment
     * @return array
     */
    private function createUrlParams(Payment $payment): array
    {
        return [
            'm' => $this->getId(),
            'oa' => $payment->getCost(),
            'o' => $payment->getUsername(),
            's' => $this->generatePaymentSign($payment)
        ];
    }

    /**
     * @param Payment $payment
     * @return string
     */
    private function generatePaymentSign(Payment $payment): string
    {
        return md5("{$this->getId()}:{$payment->getCost()}:{$this->getSecretKey1()}:{$payment->getUsername()}");
    }
}
