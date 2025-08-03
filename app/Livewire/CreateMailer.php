<?php

namespace App\Livewire;

use App\Models\Mailer;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateMailer extends Component
{
    // Form properties
    public $title = '';
    public $description = '';
    public $mail_body = '';
    public $mail_host = '';
    public $mail_port = 587;
    public $mail_username = '';
    public $mail_password = '';
    public $mail_from_address = '';
    public $mail_from_name = '';
    public $mail_encryption = 'tls';
    public $is_active = true;
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'mail_body' => 'required|string',
        'mail_host' => 'required|string|max:255',
        'mail_port' => 'required|integer|min:1|max:65535',
        'mail_username' => 'required|string|max:255',
        'mail_password' => 'required|string|max:255',
        'mail_from_address' => 'required|email|max:255',
        'mail_from_name' => 'required|string|max:255',
        'mail_encryption' => 'nullable|in:tls,ssl',
        'is_active' => 'boolean',
    ];
    
    public function createMailer()
    {
        $this->validate();
        
        Mailer::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'mail_body' => $this->mail_body,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_username' => $this->mail_username,
            'mail_password' => $this->mail_password,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
            'mail_encryption' => $this->mail_encryption,
            'is_active' => $this->is_active,
        ]);
        
        session()->flash('message', 'Mailer created successfully!');
        return redirect()->route('mailers.index');
    }
    
    public function cancel()
    {
        return redirect()->route('mailers.index');
    }

    public function render()
    {
        return view('livewire.create-mailer')
            ->layout('components.layouts.app', ['title' => 'Create New Mailer']);
    }
}
