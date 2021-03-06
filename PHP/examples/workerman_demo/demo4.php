<?php
// create-bind-listen-accept
$host = '0.0.0.0';
$port = 9999;

$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($listen_socket, $host, $port);
socket_listen($listen_socket);

// socket_recv ( resource $socket , string &$buf , int $len , int $flags ) : int
// socket socket资源
// buf 从socket中获取的数据将被保存在由 buf 制定的变量中。如果有错误发生，如链接被重置，数据不可用等等， buf 将被设为 NULL。
// len
// flags
// flags 的值可以为下列任意flag的组合。使用按位或运算符(|)来 组合不同的flag。
// MSG_OOB       处理超出边界的数据
// MSG_PEEK      从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
// MSG_WAITALL   在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
// MSG_DONTWAIT  如果制定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
while (true) {
    $connect_socket = socket_accept($listen_socket);
    // 从客户端读取信息
    $total_len = 8;
    $recv_len = 0;
    $recv_content = '';
    // 程序不会阻塞在socket_recv()这里，如果没有收到客户端数据
    // 因为用了MSG_DONTWAIT所以会立马往下执行
    $len = socket_recv($connect_socket, $content, $total_len, MSG_DONTWAIT);
    // 到了while后，一旦客户端连上来，就会不断循环
    while ($recv_len < $total_len) {
        $len = socket_recv($connect_socket, $content, ($total_len - $recv_len), MSG_DONTWAIT);
        $recv_len += $len;
        sleep(1);
        echo $recv_len . ':' . $total_len . PHP_EOL;
        if ($recv_len > 0) {
            $recv_content .= $content;
        }
    }
    echo '从客户端获取：' . $recv_content . ', 长度是：' . $recv_len . PHP_EOL;

    $msg = 'Hello World' . "\r\n";
    socket_write($connect_socket, $msg, strlen($msg));
    socket_close($connect_socket);
}
socket_close($listen_socket);