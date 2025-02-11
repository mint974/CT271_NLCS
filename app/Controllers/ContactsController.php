<?php

namespace App\Controllers;

use App\Models\Contact;

class ContactsController extends Controller
{
  public function __construct()
  {
    if (!AUTHGUARD()->isUserLoggedIn()) {
      redirect('/login');
    }

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

  public function create()
  {
    $this->sendPage('contacts/create', [
      'errors' => session_get_once('errors'),
      'old' => $this->getSavedFormValues()
    ]);
  }
  public function store()
  {
    $data = $this->filterContactData($_POST);
    $newContact = new Contact(PDO());
    $model_errors = $newContact->validate($data);
    if (empty($model_errors)) {
      $newContact->fill($data)
        ->setUser(AUTHGUARD()->user())
        ->save();
      $messages = ['success' => 'Contact has been created successfully.'];
      redirect('/',  $messages);
    }
    // Lưu các giá trị của form vào $_SESSION['form']
    $this->saveFormValues($_POST);
    // Lưu các thông báo lỗi vào $_SESSION['errors']
    redirect('/contacts/add', ['errors' => $model_errors]);
  }
  protected function filterContactData(array $data)
  {
    return [
      'name' => $data['name'] ?? '',
      'phone' => $data['phone'] ?? '',
      'notes' => $data['notes'] ?? ''
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
