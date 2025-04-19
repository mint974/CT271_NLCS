<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Promotion;
use Error;

class PromotionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($error = null, $success = null)
    {
        // dd((new Promotion(PDO()))->getAll());
        $this->sendPage('promotions/index', [
            'promotions' => (new Promotion(PDO()))->getAll(),
            'errors' => $error,
            'success' => $success
        ]);
    }

    public function storepage()
    {
        $this->sendPage('promotions/add');
    }

    public function store()
    {

        $errors = [];
        if (!$this->checkCsrf()) {
            $errors['Crsf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }

        $data = $this->filterPromotionData($_POST);

        $promotion = new Promotion(PDO());
        $errors = $promotion->validate($data);


        if (empty($errors)) {
            // dd($promotion->fill($data)->save());
            if (!$promotion->fill($data)->save()) {
                $errors['save'] = 'Không thể lưu khuyến mãi.';
            } else {

                $success = 'Đã thêm khuyến mãi thành công.';
                $promotion = (new Promotion(pdo()))->getAll();

                $this->sendPage('promotions/index', [
                    'promotions' => $promotion,
                    'success' => $success
                ]);
            }
        }

        $this->saveFormValues($_POST);

        $this->sendPage('/promotions/add', ['errors' => $errors]);
    }

    public function updatepage($id)
    {
        $promotion = (new Promotion(pdo()))->find($id);
        $this->sendPage('promotions/update', [
            'promotion' => $promotion
        ]);
    }

    public function update()
    {
        $errors = [];
    
        if (!$this->checkCsrf()) {
            $errors['csrf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
        }
    
        $data = $this->filterPromotionData($_POST);
        $id = $data['id_promotion'] ?? null;
    
        $promotionModel = new Promotion(pdo());
        $existingPromotion = $promotionModel->find($id);
    
        if (!$existingPromotion) {
            $errors['not_found'] = 'Không tìm thấy khuyến mãi cần cập nhật.';
        } else {
            $errors = $promotionModel->validate($data);
        }
    
        if (empty($errors)) {
            $promotion = new Promotion(pdo());
            $promotion->fill($data);
            $promotion->update();
    
            
            redirect('/promotions/admin', ['success' => 'Cập nhật thông tin cho ' . $promotion->id_promotion . ' thành công!']);
        }
    

        $this->saveFormValues($_POST);
        $_SESSION['errors'] = $errors;
        redirect('/promotions/update/' . $id, ['errors' => $errors]);
    }



    protected function filterPromotionData(array $data)
    {
        return [
            'id_promotion' => $data['id_promotion'] ?? '',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'discount_percent' => $data['discount_percent'] ?? '',
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? ''
        ];
    }

    public function fillformsearch(array $data)
    {
        $promotion['id_promotion'] = isset($data['id_promotion']) ? htmlspecialchars($data['id_promotion']) : '';
        $promotion['name'] = isset($data['name']) ? htmlspecialchars($data['name']) : '';
        $promotion['StartDay'] = isset($data['start_day']) ? htmlspecialchars($data['start_day']) : '';
        $promotion['EndDay'] = isset($data['end_day']) ? htmlspecialchars($data['end_day']) : '';

        return $promotion;
    }



    public function search()
    {

        if (!$this->checkCsrf()) {
            $error = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
            $this->index($error);
            exit();
        }

        $promotion = $this->fillformsearch($_POST);

        // dd($promotion);

        $prmotionModel = new Promotion(pdo());
        if (!empty($promotion['id_promotion'])) {
            $promotionById = $prmotionModel->find($promotion['id_promotion']);
            $promotions = $promotionById ? [$promotionById] : [];


        } elseif (!empty($promotion['name'])) {
            $promotions = $prmotionModel->where('name', $promotion['name']);
        } elseif (!empty($promotion['StartDay'])) {
            $promotions = $prmotionModel->where('start_day', $promotion['StartDay']);
        } elseif (!empty($promotion['EndDay'])) {
            $promotions = $prmotionModel->where('end_day', $promotion['EndDay']);
        } else {
            $error = "lỗi tìm kiếm";
        }

        $this->sendPage('promotions/index', [
            'promotions' => $promotions,
            'errors' => $error ?? null
        ]);
    }
}
