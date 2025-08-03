<?php

namespace App\Livewire;

use App\Models\Mailer;
use App\Models\MailerItem;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MailerItemManager extends Component
{
    use WithPagination;

    public $mailerId;
    public $mailer;
    public $showCreateForm = false;
    public $showEditForm = false;
    public $editingItem = null;
    
    // Form properties
    public $recipient_email = '';
    public $recipient_name = '';
    public $send_at = '';
    
    // Search and filter
    public $search = '';
    public $filterStatus = 'all';
    
    protected $rules = [
        'recipient_email' => 'required|email|max:255',
        'recipient_name' => 'required|string|max:255',
        'send_at' => 'nullable|date|after_or_equal:now',
    ];
    
    public function mount($mailerId = null)
    {
        if ($mailerId) {
            $this->mailerId = $mailerId;
            $this->mailer = Mailer::where('user_id', Auth::id())->findOrFail($mailerId);
        }
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function showCreateForm()
    {
        $this->resetForm();
        $this->showCreateForm = true;
    }
    
    public function hideCreateForm()
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }
    
    public function createItem()
    {
        $this->validate();
        
        MailerItem::create([
            'mailer_id' => $this->mailerId,
            'recipient_email' => $this->recipient_email,
            'recipient_name' => $this->recipient_name,
            'status' => 'pending',
            'sent_at' => $this->send_at ? Carbon::parse($this->send_at) : null,
        ]);
        
        $this->hideCreateForm();
        session()->flash('message', 'Recipient added successfully!');
    }
    
    public function editItem($itemId)
    {
        $item = MailerItem::where('mailer_id', $this->mailerId)->findOrFail($itemId);
        
        $this->editingItem = $item;
        $this->recipient_email = $item->recipient_email;
        $this->recipient_name = $item->recipient_name;
        $this->send_at = $item->sent_at ? $item->sent_at->format('Y-m-d\TH:i') : '';
        
        $this->showEditForm = true;
    }
    
    public function updateItem()
    {
        $this->validate();
        
        $this->editingItem->update([
            'recipient_email' => $this->recipient_email,
            'recipient_name' => $this->recipient_name,
            'sent_at' => $this->send_at ? Carbon::parse($this->send_at) : null,
        ]);
        
        $this->hideEditForm();
        session()->flash('message', 'Recipient updated successfully!');
    }
    
    public function hideEditForm()
    {
        $this->showEditForm = false;
        $this->editingItem = null;
        $this->resetForm();
    }
    
    public function deleteItem($itemId)
    {
        $item = MailerItem::where('mailer_id', $this->mailerId)->findOrFail($itemId);
        $item->delete();
        
        session()->flash('message', 'Recipient deleted successfully!');
    }
    
    public function markAsSent($itemId)
    {
        $item = MailerItem::where('mailer_id', $this->mailerId)->findOrFail($itemId);
        $item->markAsSent();
        
        session()->flash('message', 'Item marked as sent!');
    }
    
    public function markAsFailed($itemId)
    {
        $item = MailerItem::where('mailer_id', $this->mailerId)->findOrFail($itemId);
        $item->markAsFailed('Manually marked as failed');
        
        session()->flash('message', 'Item marked as failed!');
    }
    
    public function resetToPending($itemId)
    {
        $item = MailerItem::where('mailer_id', $this->mailerId)->findOrFail($itemId);
        $item->update([
            'status' => 'pending',
            'sent_at' => null,
            'delivered_at' => null,
            'error_message' => null,
        ]);
        
        session()->flash('message', 'Item reset to pending!');
    }
    
    public function bulkImport()
    {
        // This would handle CSV import functionality
        // For now, we'll just show a placeholder
        session()->flash('message', 'Bulk import feature coming soon!');
    }
    
    private function resetForm()
    {
        $this->recipient_email = '';
        $this->recipient_name = '';
        $this->send_at = '';
    }
    
    public function render()
    {
        $query = MailerItem::where('mailer_id', $this->mailerId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('recipient_email', 'like', '%' . $this->search . '%')
                      ->orWhere('recipient_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest();
            
        $items = $query->paginate(15);
        
        return view('livewire.mailer-item-manager', compact('items'));
    }
}
