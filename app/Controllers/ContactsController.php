<?php

namespace App\Controllers;

use App\Models\Contact;

class ContactsController extends Controller
{
  public function __construct()
  {


    parent::__construct();
  }

  public function index()
  {
    $success = session_get_once('success');
    $this->sendPage('contacts/index', [
      'contacts' => AUTHGUARD()->user()?->contacts() ?? [],
      'success' => $success
    ]);
  }

  public function indexUser()
  {
    $user = AUTHGUARD()->user();
    unset($user->password);
    // dd($user);
    // exit();
    $this->sendPage('contacts/index', [
      'user' => $user
    ]);

  }

  public function create()
  {
    $this->sendPage('contacts/create', [
      'errors' => session_get_once('errors'),
      'old' => $this->getSavedFormValues()
    ]);
  }
  public function store()
  {
    $user = AUTHGUARD()->user();
    unset($user->password);
    $data = $this->filterContactData($_POST);

    $newContact = new Contact(PDO());
    $model_errors = $newContact->validate($data);

    if (!$this->checkCsrf()) {
      $model_errors['Crsf'] = 'Lỗi CSRF, hãy kiểm tra và thử lại!';
    }
    if (empty($model_errors)) {
      if (!$newContact->fill($data)->save()) {
        $model_errors['save'] = 'Lỗi thêm liên hệ';
      } else {
        $messages = ['success' => 'Thêm liên hệ thành công, vui lòng kiểm tra email thường xuyên để cập nhật.'];
        $this->sendPage('contacts/index', [
          'user' => $user,
          'success' => $messages
        ]);
      }
    }
    // Lưu các giá trị của form vào $_SESSION['form']
    $this->saveFormValues($_POST);
    // Lưu các thông báo lỗi vào $_SESSION['errors']
    $this->sendPage('contacts/index', [
      'user' => $user,
      'errors' => $model_errors
    ]);
  }


  protected function filterContactData(array $data)
  {
    return [
      'name' => $data['username'] ?? '',
      'email' => $data['email'] ?? '',
      'phone' => $data['phone'] ?? '',
      'subject' => $data['subject'] ?? '',
      'content' => $data['content'] ?? ''
    ];
  }

  public function edit($contactId)
  {
    $contact = AUTHGUARD()->user()->findContact($contactId);
    if (!$contact) {
      $this->sendNotFound();
    }
    $form_values = $this->getSavedFormValues();
    $data = [
      'errors' => session_get_once('errors'),
      'contact' => (!empty($form_values)) ?
        array_merge($form_values, ['id' => $contact->id]) :
        (array) $contact
    ];
    $this->sendPage('contacts/edit', $data);
  }
  public function update($contactId)
  {
    $contact = AUTHGUARD()->user()->findContact($contactId);
    if (!$contact) {
      $this->sendNotFound();
    }
    $data = $this->filterContactData($_POST);
    $model_errors = $contact->validate($data);
    if (empty($model_errors)) {
      $contact->fill($data);
      $contact->save();
      $messages = ['success' => 'Contact has been updated successfully.'];
      redirect('/', $messages);
    }
    $this->saveFormValues($_POST);
    redirect('/contacts/edit/' . $contactId, [
      'errors' => $model_errors
    ]);
  }

  public function destroy($contactId)
  {
    $contact = AUTHGUARD()->user()->findContact($contactId);
    if (!$contact) {
      $this->sendNotFound();
    }
    $contact->delete();
    $messages = ['success' => 'Contact has been deleted successfully.'];
    redirect('/', $messages);
  }
}
