<?php

set_time_limit(0);
while (!file_get_contents('http://telegram-bot.dev-lead.ru/')) {
    file_get_contents('https://api.telegram.org/bot1913717295:AAH0QLrCiQLyeJt4BVB_sctJR1b5K3SNZYk/sendMessage?chat_id=-657050211&text=noLink');
    sleep(60 * 10);
}

file_get_contents('https://api.telegram.org/bot1913717295:AAH0QLrCiQLyeJt4BVB_sctJR1b5K3SNZYk/sendMessage?chat_id=-657050211&text=IsOk');

?>