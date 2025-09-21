@extends('layouts.admin')

@section('title', 'Detail Pesan')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Detail Pesan</h2>
        <a href="{{ route('admin.messages') }}" class="card-action">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
    
    <div style="padding: 32px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
            <!-- Message Information -->
            <div class="info-section">
                <div class="info-section-header">
                    <h3>Informasi Pesan</h3>
                </div>
                
                <div class="info-section-content">
                    <div class="detail-item">
                        <label class="detail-label">Status</label>
                        @if($message->status === 'unread')
                            <span class="status-badge status-pending">
                                <i class="fas fa-envelope"></i>
                                Belum Dibaca
                            </span>
                        @elseif($message->status === 'read')
                            <span class="status-badge status-approved">
                                <i class="fas fa-envelope-open"></i>
                                Sudah Dibaca
                            </span>
                        @else
                            <span class="status-badge status-completed">
                                <i class="fas fa-reply"></i>
                                Sudah Dibalas
                            </span>
                        @endif
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Subjek</label>
                        <p class="detail-value">{{ $message->subject }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Tanggal Dikirim</label>
                        <p class="detail-value">{{ $message->created_at->format('d F Y H:i') }}</p>
                    </div>
                    
                    @if($message->status === 'replied' && $message->replied_at)
                    <div class="detail-item">
                        <label class="detail-label">Tanggal Dibalas</label>
                        <p class="detail-value">{{ $message->replied_at->format('d F Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Sender Information -->
            <div class="info-section">
                <div class="info-section-header">
                    <h3>Informasi Pengirim</h3>
                </div>
                
                <div class="info-section-content">
                    <div class="detail-item">
                        <label class="detail-label">Nama Lengkap</label>
                        <p class="detail-value">{{ $message->name }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Email</label>
                        <p class="detail-value">{{ $message->email }}</p>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Nomor Telepon</label>
                        <p class="detail-value">{{ $message->phone ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Message Content -->
        <div class="info-section" style="margin-bottom: 2rem;">
            <div class="info-section-header">
                <h3>Isi Pesan</h3>
            </div>
            
            <div class="info-section-content">
                <div class="message-content">
                    {{ $message->message }}
                </div>
            </div>
        </div>
        
        <!-- Admin Reply -->
        @if($message->status === 'replied' && $message->admin_reply)
        <div class="info-section" style="margin-bottom: 2rem;">
            <div class="info-section-header">
                <h3>Balasan Admin</h3>
            </div>
            
            <div class="info-section-content">
                <div class="message-content">
                    {{ $message->admin_reply }}
                </div>
            </div>
        </div>
        @endif
        
        <!-- Reply Form -->
        @if($message->status !== 'replied')
        <div class="info-section">
            <div class="info-section-header">
                <h3>Balas Pesan</h3>
            </div>
            
            <div class="info-section-content">
                <form method="POST" action="{{ route('admin.messages.reply', $message->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="reply">Balasan</label>
                        <textarea name="reply" id="reply" rows="6" class="form-control" placeholder="Tulis balasan Anda..." required>{{ old('reply') }}</textarea>
                        @error('reply')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-reply"></i>
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Import CSS dari booking-detail untuk konsistensi */
.info-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.info-section-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: white;
    padding: 1.5rem;
    margin: 0;
}

.info-section-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: white;
}

.info-section-content {
    padding: 2rem;
}

.detail-item {
    margin-bottom: 1.5rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    color: #1e293b;
    font-weight: 600;
    font-size: 1rem;
    margin: 0;
    line-height: 1.5;
}

.message-content {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    line-height: 1.6;
    color: #1e293b;
    white-space: pre-wrap;
}

.status-completed {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
    color: #065f46;
    border: 1px solid rgba(16, 185, 129, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn-primary {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

@media (max-width: 768px) {
    div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
    }
    
    .info-section-header,
    .info-section-content {
        padding: 1rem !important;
    }
}
</style>
@endsection
