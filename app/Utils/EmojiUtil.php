<?php


namespace App\Utils;


class EmojiUtil
{
  static function numberInEmoji($str) {
    if (strlen($str) > 1) return self::numberInEmoji($str[0]) . self::numberInEmoji(substr($str, 1));
    $number = intval($str);
    if ($number == 0) return "0️⃣";
    if ($number == 1) return "1️⃣";
    if ($number == 2) return "2️⃣";
    if ($number == 3) return "3️⃣";
    if ($number == 4) return "4️⃣";
    if ($number == 5) return "5️⃣";
    if ($number == 6) return "6️⃣";
    if ($number == 7) return "7️⃣";
    if ($number == 8) return "8️⃣";
    if ($number == 9) return "9️⃣";
    return "";
  }

}