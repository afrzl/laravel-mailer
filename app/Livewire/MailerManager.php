<?php

namespace App\Livewire;

use App\Models\Mailer;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class MailerManager extends Component
{
    use WithPagination;

    public $showEditForm = false;
    public $editingMailer = null;
    
    // Form properties for edit
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
    
    // Search and filter
    public $search = '';
    public $filterActive = 'all';
    
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
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterActive()
    {
        $this->resetPage();
    }
    

    
    public function editMailer($mailerId)
    {
        $mailer = Mailer::where('user_id', Auth::id())->findOrFail($mailerId);
        
        $this->editingMailer = $mailer;
        $this->title = $mailer->title;
        $this->description = $mailer->description;
        $this->mail_body = $mailer->mail_body;
        $this->mail_host = $mailer->mail_host;
        $this->mail_port = $mailer->mail_port;
        $this->mail_username = $mailer->mail_username;
        $this->mail_password = $mailer->mail_password;
        $this->mail_from_address = $mailer->mail_from_address;
        $this->mail_from_name = $mailer->mail_from_name;
        $this->mail_encryption = $mailer->mail_encryption;
        $this->is_active = $mailer->is_active;
        
        $this->showEditForm = true;
    }
    
    public function updateMailer()
    {
        $this->validate();
        
        $this->editingMailer->update([
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
        
        $this->hideEditForm();
        session()->flash('message', 'Mailer updated successfully!');
    }
    
    public function hideEditForm()
    {
        $this->showEditForm = false;
        $this->editingMailer = null;
        $this->resetForm();
    }
    
    public function deleteMailer($mailerId)
    {
        $mailer = Mailer::where('user_id', Auth::id())->findOrFail($mailerId);
        $mailer->delete();
        
        session()->flash('message', 'Mailer deleted successfully!');
    }
    
    public function toggleActive($mailerId)
    {
        $mailer = Mailer::where('user_id', Auth::id())->findOrFail($mailerId);
        $mailer->update(['is_active' => !$mailer->is_active]);
        
        session()->flash('message', 'Mailer status updated!');
    }
    
    private function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->mail_body = '';
        $this->mail_host = '';
        $this->mail_port = 587;
        $this->mail_username = '';
        $this->mail_password = '';
        $this->mail_from_address = '';
        $this->mail_from_name = '';
        $this->mail_encryption = 'tls';
        $this->is_active = true;
    }
    
    public function render()
    {
        $query = Mailer::where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('mail_from_address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterActive !== 'all', function ($query) {
                $query->where('is_active', $this->filterActive === 'active');
            })
            ->withCount(['items', 'pendingItems', 'sentItems', 'failedItems'])
            ->latest();
            
        $mailers = $query->paginate(10);
        
        return view('livewire.mailer-manager', compact('mailers'));
    }
}
