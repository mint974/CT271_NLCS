<?php

namespace App\Controllers;

use App\Models\Payment;
use DateTime;
class PaymentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function FillFormUrl($data)
    {
        return [
            'id_order' => $data['vnp_TxnRef'] ?? '',
            'payment_method' => 'Online',
            'payment_status' => ($data['vnp_ResponseCode'] ?? '') === '00' ? 'Đã thanh toán' : 'Thất bại',
            'transaction_code' => $data['vnp_TransactionNo'] ?? null,
            'payment_time' => isset($data['vnp_PayDate']) ? $this->formatVnpayDate($data['vnp_PayDate']) : null,
        ];
    }

    private function formatVnpayDate($vnpDate)
    {
        // vnp_PayDate: "20250419102657" => "2025-04-19 10:26:57"
        return DateTime::createFromFormat('YmdHis', $vnpDate)?->format('Y-m-d H:i:s');
    }


    public function store()
    {
        // dd($_GET);

        $errors = [];
        $data = $this->FillFormUrl($_GET);
        $payment = new Payment(pdo());

        $errors = $payment->validate($data);

        if (empty($errors)) {
            if (!$payment->fill($data)->save()) {
                $errors['save'] = 'Không thể lưu thanh toán.';
            } else {
                $success = 'Thanh toán thành công';
                redirect('/orders/index', [
                    'success' => $success
                ]);
            }
        }

        redirect('/orders/index', [
            'errors' => $errors
        ]);
    }

    public function storeCOD()
    {
       
        $payment = new Payment(pdo());
        $payment->id_order = $_SESSION['data']['id_order'];
        $payment->payment_method = 'COD';
        $payment->payment_status = 'Chưa thanh toán';
        $payment->payment_time = $_SESSION['data']['created_at'];
        $payment->transaction_code = null;

        if (!$payment->save()) {
            redirect('/orders/index', [
                'errors' => ['save' => 'Không thể lưu thanh toán.']
            ]);
        }

        redirect('/orders/index', [
            'success' => 'Thanh toán thành công'
        ]);
    }

    public function updatepage($id)
    {
        $payment = (new Payment(pdo()))->find($id);
        $this->sendPage('payments/update', [
            'payment' => $payment
        ]);

    }

    public function update()
    {
        $errors = [];

        if (!$this->checkCsrf()) {
            $errors['csrf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        $data = $this->filterPaymentData($_POST);
        $id = $data['id_payment'] ?? null;

        $paymentModel = new Payment(pdo());
        $existing = $paymentModel->find($id);

        if (!$existing) {
            $errors['not_found'] = 'Không tìm thấy thanh toán cần cập nhật.';
        } else {
            $errors = $paymentModel->validate($data);
        }

        if (empty($errors)) {
            $payment = new Payment(pdo());
            $payment->fill($data)->save();

            redirect('/payments/admin', ['success' => 'Cập nhật thành công cho thanh toán #' . $payment->id_payment]);
        }

        $this->saveFormValues($_POST);
        $_SESSION['errors'] = $errors;
        redirect('/payments/update/' . $id, ['errors' => $errors]);
    }

    protected function filterPaymentData(array $data)
    {
        return [
            'id_payment' => $data['id_payment'] ?? '',
            'id_order' => $data['id_order'] ?? '',
            'payment_method' => $data['payment_method'] ?? 'COD',
            'payment_status' => $data['payment_status'] ?? 'Chưa thanh toán',
            'transaction_code' => $data['transaction_code'] ?? null,
            'payment_time' => $data['payment_time'] ?? null,
        ];
    }

    protected function fillformsearch(array $data)
    {
        return [
            'id_payment' => isset($data['id_payment']) ? htmlspecialchars($data['id_payment']) : '',
            'payment_method' => isset($data['payment_method']) ? htmlspecialchars($data['payment_method']) : ''
        ];
    }

    public function search()
    {
        if (!$this->checkCsrf()) {
            $error = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
            $this->index($error);
            exit();
        }

        $searchData = $this->fillformsearch($_POST);
        $paymentModel = new Payment(pdo());

        if (!empty($searchData['id_payment'])) {
            $payment = $paymentModel->find($searchData['id_payment']);
            $payments = $payment ? [$payment] : [];
        } else {
            $payments = $paymentModel->getAll(); // Có thể thay bằng where nếu cần tìm theo method
        }

        $this->sendPage('payments/index', [
            'payments' => $payments ?? [],
            'errors' => $error ?? null
        ]);
    }
}
