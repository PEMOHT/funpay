<?php

/**
 * Class SmsParserResult
 */
class SmsParserResult
{
    /**
     * @var string
     */
    private $sum;
    /**
     * @var string
     */
    private $wallet;
    /**
     * @var string
     */
    private $password;

    /**
     * SmsParserResult constructor.
     * @param string $sum
     * @param string $wallet
     * @param string $password
     */
    private function __construct(
        string $sum,
        string $wallet,
        string $password
    )
    {
        $this->sum = $sum;
        $this->wallet = $wallet;
        $this->password = $password;
    }

    /**
     * @param string $sms
     * @return SmsParserResult|null ?static
     */
    public static function createFrom(string $sms): ?self
    {
        preg_match('#\d+[,.]\d{2}#', $sms, $matches);

        if (is_null($sum = $matches[0])) {
            return null;
        }

        preg_match_all("#\d+#", $sms, $matches);

        $short = $long = null;
        foreach ($matches[0] as $match) {
            // убираем из совпадений куски от суммы
            if (strpos($sum, $match) !== FALSE) {
                continue;
            }
            // первый найденный будет паролем
            if (is_null($short)) {
                $short = $match;
                continue;
            }
            // если в первый раз это был не пароль, то поменяем их местами
            if (strlen($short) > strlen($match)) {
                $long = $short;
                $short = $match;
            } else {
                $long = $match;
            }
        }

        if (is_null($short) || is_null($long)) {
            return null;
        }

        return new self($sum, $long, $short);
    }

    /**
     * @return string
     */
    public function getSum(): string
    {
        return $this->sum;
    }

    /**
     * @return string
     */
    public function getWallet(): string
    {
        return $this->wallet;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}

$smsSample = "sdgsdgsdg 410017718723922 фывфыв: 8429
ячваивяа 0,02р
";


$result = SmsParserResult::createFrom($smsSample);
if ($result instanceof SmsParserResult) {
    echo implode("\n", [
        "Кошелек " . $result->getWallet(),
        "Пароль " . $result->getPassword(),
        "Сумма " . $result->getSum(),
    ]);
} else {
    echo "Парсинг не удался";
}
