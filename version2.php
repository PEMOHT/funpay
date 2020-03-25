<?php


function parse(string $sms): ?array
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

    return [
        'sum' => $sum,
        'wallet' => $long,
        'password' => $short
    ];
}

$smsSample = "sdgsdgsdg 410017718723922 фывфыв: 8429
ячваивяа 0,02р
";

$result = parse($smsSample);
if (is_array($result)) {
    echo implode("\n", ["Кошелек $result[wallet]", "Пароль $result[password]", "Сумма $result[sum]"]);
} else {
    echo "Парсинг не удался";
}
