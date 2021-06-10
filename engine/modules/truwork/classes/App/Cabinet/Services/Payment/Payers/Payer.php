<?php


namespace App\Cabinet\Services\Payment\Payers;


use App\Cabinet\Entity\PaymentHistory;
use App\Cabinet\Models\PaymentHistoryModel;
use App\Core\Application;
use App\Core\Entity\User;
use App\Core\Exceptions\Exception;
use App\Core\Http\Request;

abstract class Payer
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PaymentHistoryModel
     */
    private $paymentHistoryModel;

    /**
     * Payer constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return PaymentHistoryModel
     */
    protected function getPaymentHistoryModel(): PaymentHistoryModel
    {
        if (is_null($this->paymentHistoryModel)) {
            $this->paymentHistoryModel = Application::getInstance()->make(PaymentHistoryModel::class);
        }

        return $this->paymentHistoryModel;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function init(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config['enabled'] ?? false;
    }

    /**
     * @param bool $val
     * @return $this
     */
    public function setIsEnabled(bool $val)
    {
        $this->config['enabled'] = $val;

        return $this;
    }

    /**
     * @param User $user
     * @return string
     */
    public function complete(User $user): string
    {
        $this->getPaymentHistoryModel()->insert(PaymentHistory::createEntity($user, $this->name(), $this->getSum()));
        return 'OK';
    }

    /**
     * @return string
     */
    abstract public function name(): string;

    /**
     * @param User $user
     * @param int $cost
     * @return string
     */
    abstract public function paymentUrl(User $user, int $cost): string;

    /**
     * @return string|int
     */
    abstract public function getUsername();

    /**
     * @return int
     */
    abstract public function getSum(): int;

    /**
     * @return bool
     * @throws Exception
     */
    abstract public function validate(): bool;

    /**
     * @param string $message
     * @return string
     */
    abstract public function successResponse(string $message): string;

    /**
     * @param string $message
     * @return string
     */
    abstract public function errorResponse(string $message): string;
}
