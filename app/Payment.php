<?php
/**
 * This file is part of Robokassa package.
 *
 * (c) 2014 IDM Agency (http://idma.ru)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App;
/**
 * Class Payment
 *
 * @author Stringnick <string.nick@yandex.ru>
 *
 * @package Freekassa
 */
class Payment {
    const CULTURE_EN = 'en';
    const CULTURE_RU = 'ru';
    private $baseUrl      = 'http://www.free-kassa.ru/merchant/cash.php?';
    private $valid        = false;
    private $data;
    private $merchantId;
    private $secret1;
    private $secret2;

    /**
     * Payment constructor.
     * @param $merchantId int id магазина
     * @param $secret1 string секретное слово1
     * @param $secret2 string секретное слово2
     */
    public function __construct($merchantId, $secret1, $secret2)
    {
        $this->merchantId = $merchantId;
        $this->secret1 = $secret1;
        $this->secret2 = $secret2;
        $this->data = [
            'm'  => $this->merchantId,
            'o'          => null,
            'oa'         => 0,
            's' => '',
            'lang'        => self::CULTURE_RU,
        ];
    }

    /**
     * @return bool|string
     */
    public function getPaymentUrl()
    {
        if ($this->data['oa'] <= 0) {
            return true;
        }
        if (empty($this->data['o'])) {
            return true;
        }

        $this->data['s'] = md5($this->merchantId.':'.$this->data['oa'].':'.$this->secret1.':'.$this->data['o']);
        $data   = http_build_query($this->data, null, '&');
        return $this->baseUrl . $data;
    }
    /**
     * Validates on ResultURL.
     *
     * @param  string $data query data
     *
     * @return bool
     */
    public function validateResult($data)
    {
        return $this->validate($data);
    }
    /**
     * Validates on SuccessURL.
     *
     * @param  string $data query data
     *
     * @return bool
     */
    public function validateSuccess($data)
    {
        return $this->validate($data);
    }
    /**
     * Validates the Robokassa query.
     *
     * @param  string $data         query data
     * @param  string $passwordType type of password, 'validation' or 'payment'
     *
     * @return bool
     */
    private function validate($data)
    {
        $this->data = $data;
        $signature = $this->merchantId.':'.$data['oa'].':'.$this->secret2.':'.$data['o'];
        $this->valid = (md5($signature) === strtolower($data['s']));
        return $this->valid;
    }
    /**
     * Returns whether the Robokassa query is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }
    /**
     * @return string
     */
    public function getSuccessAnswer() {
        return 'OK' . $this->getInvoiceId() . "\n";
    }
    /**
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->data['o'];
    }
    /**
     * @param $id
     *
     * @return Payment
     */
    public function setInvoiceId($id)
    {
        $this->data['o'] = (int) $id;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->data['oa'];
    }
    /**
     * @param  mixed $summ
     *
     * @throws InvalidSumException
     *
     * @return Payment
     */
    public function setSum($summ)
    {
        if ($summ > 0) {
            $this->data['oa'] = $summ;
            return $this;
        } else {
            throw new InvalidSumException();
        }
    }
    /**
     * @return string
     */
    public function getCulture()
    {
        return $this->data['lang'];
    }
    /**
     * @param  string $culture
     *
     * @return Payment
     */
    public function setCulture($culture = self::CULTURE_RU)
    {
        $this->data['lang'] = (string) $culture;
        return $this;
    }
    /**
     * @return string
     */
    public function getCurrencyLabel()
    {
        return $this->data['i'];
    }
    /**
     * @param  string $currLabel
     *
     * @return Payment
     */
    public function setCurrencyLabel($currLabel)
    {
        $this->data['i'] = (string) $currLabel;
        return $this;
    }
}