<?php

namespace App\Models;

use PDO;

class Payment
{
    private PDO $db;

    public string $id_payment;
    public string $id_order;
    public string $payment_method;
    public string $payment_status;
    public ?string $transaction_code;
    public ?string $payment_time;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function save(): bool
    {
            $statement = $this->db->prepare(
                'INSERT INTO payments 
                 ( id_order, payment_method, payment_status, transaction_code, payment_time)
                 VALUES 
                 ( :id_order, :payment_method, :payment_status, :transaction_code, :payment_time)'
            );

            return $statement->execute([
                'id_order' => $this->id_order,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'transaction_code' => $this->transaction_code,
                'payment_time' => $this->payment_time
            ]);
        }
    

    public function find(string $id): ?Payment
    {
        $statement = $this->db->prepare('SELECT * FROM payments WHERE id_order = :id_order');
        $statement->execute(['id_order' => $id]);

        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            return $this->fillFromDbRow($row);
        }

        return null;
    }


    private function fillFromDbRow(array $row): Payment
    {
        $this->id_payment = $row['id_payment'];
        $this->id_order = $row['id_order'];
        $this->payment_method = $row['payment_method'];
        $this->payment_status = $row['payment_status'];
        $this->transaction_code = $row['transaction_code'];
        $this->payment_time = $row['payment_time'];

        return $this;
    }

    public function getAll(): array
    {
        $statement = $this->db->prepare('SELECT * FROM payments ORDER BY payment_time DESC');
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($rows as $row) {
            $result[] = (new Payment($this->db))->fillFromDbRow($row);
        }

        return $result;
    }

    public function fill($data)
    {
        $payment = new Payment(pdo());
        $payment->id_order = $data['id_order'] ?? '';
        $payment->payment_method = $data['payment_method'] ?? 'COD';
        $payment->payment_status = $data['payment_status'] ?? 'Chưa thanh toán';
        $payment->transaction_code = $data['transaction_code'] ?? null;
        $payment->payment_time = $data['payment_time'] ?? now();

        return $payment;

    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['id_order'])) {
            $errors['id_order'] = 'Đơn hàng không được để trống.';
        }

        if (!in_array($data['payment_method'], ['Online', 'COD'])) {
            $errors['payment_method'] = 'Phương thức thanh toán không hợp lệ.';
        }

        if (!in_array($data['payment_status'], ['Chưa thanh toán', 'Đã thanh toán', 'Thất bại'])) {
            $errors['payment_status'] = 'Trạng thái thanh toán không hợp lệ.';
        }

        return $errors;
    }



    public function pay($vnp_Returnurl, $vnp_TxnRef, $vnp_OrderInfo, $vnp_OrderType, $vnp_Amount)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_TmnCode = "JIBMOZHC";//Mã website tại VNPAY
        $vnp_HashSecret = "13L00ZHVZ1VGJVL0W93Y05GVJD3X8PHP"; //Chuỗi bí mật


        $vnp_Amount = $vnp_Amount * 100; // số tiền thanh toán, phải nhân 100 để đưa về đơn vị tiền tệ nhỏ nhất
        $vnp_Locale = "vn"; // ngôn ngữ hiển thị trên cổng thanh toán
        $vnp_BankCode = "NCB"; // mã Ngân hàng thanh toán
        $vnp_IpAddr = "127.0.0.1"; // Địa chỉ IP người mua
        $inputData = array(
            "vnp_Version" => "2.1.0", // phiên bản vnPay
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }


        $returnData = array(
            'code' => '00'
            ,
            'message' => 'success'
            ,
            'data' => $vnp_Url
        );


        if (isset($_POST['redirect'])) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            // echo json_encode($returnData);
        }

        // Tài khoản test:
        // Ngân hàng: NCB
        // Số thẻ: 9704198526191432198
        // Tên chủ thẻ:NGUYEN VAN A
        // Ngày phát hành:07/15
        // Mật khẩu OTP:123456
    }


}
