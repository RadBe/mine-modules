<?php


namespace App\Cabinet\Services\Payment\Payers\Unitpay;


use App\Cabinet\Services\Payment\Payers\Payer as ContractPayer;
use App\Core\Entity\User;
use App\Core\Exceptions\Exception;

class Payer extends ContractPayer
{
    private const URL = 'https://unitpay.ru/pay';

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'unitpay';
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
     * @inheritDoc
     */
    public function paymentUrl(User $user, int $cost): string
    {
        $description = "Пополнение счета. Аккаунт: {$user->name}. Сумма: $cost руб.";
        $payment = new Payment($user->name, $cost, $description);

        return static::URL . '/' . $this->getPublicKey() . '?' . http_build_query($this->createUrlParams($payment));
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->request->any('params', [])['account'];
    }

    /**
     * @inheritDoc
     */
    public function getSum(): int
    {
        return (int) $this->request->any('params', [])['sum'];
    }

    /**
     * @inheritDoc
     */
    public function validate(): bool
    {
        $data = $this->request->any();
        if ((int) ($data['params']['sum'] ?? 0) < 1) {
            throw new Exception('Сумма должна быть больше 0!');
        }

        $signature = $data['params']['signature'];
        $params = $data['params'];
        ksort($params);
        unset($params['sign']);
        unset($params['signature']);

        array_push($params, $this->getSecretKey());
        array_unshift($params, $data['method']);

        return $signature === hash('sha256', join('{up}', $params));
    }

    /**
     * @inheritDoc
     */
    public function successResponse(string $message): string
    {
        header('Content-Type: application/json');
        return json_encode(['result' => ['message' => $message]]);
    }

    /**
     * @inheritDoc
     */
    public function errorResponse(string $message): string
    {
        header('Content-Type: application/json');
        return json_encode(['error' => ['message' => $message]]);
    }

    /**
     * @inheritDoc
     */
    public function complete(User $user): string
    {
        if ($this->request->any('method') != 'pay') {
            print $this->successResponse('OK');
            die;
        }

        return parent::complete($user);
    }

    /**
     * @param Payment $payment
     * @return array
     */
    private function createUrlParams(Payment $payment): array
    {
        return [
            'account' => $payment->getUsername(),
            'sum' => $payment->getCost(),
            'desc' => $payment->getDescription(),
            'signature' => $this->createSignature($payment),
            'hideMenu' => 'true',
            'operator' => 'qiwi',
            'currency' => $payment->getCurrency(),
        ];
    }

    /**
     * @param Payment $payment
     * @return string
     */
    private function createSignature(Payment $payment): string
    {
        return hash(
            'sha256',
            $payment->getUsername() . '{up}' . $payment->getCurrency() . '{up}' . $payment->getDescription() . '{up}' . $payment->getCost() . '{up}' . $this->getSecretKey()
        );
    }
}
