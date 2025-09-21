@extends('layouts.admin')

@section('title', 'Kelola Pesan')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Kelola Pesan</h2>
        <div class="card-stats">
            <span class="stat-item">
                <i class="fas fa-envelope"></i>
                Total: {{ $messages->total() }}
            </span>
            @if($unreadCount > 0)
            <span class="stat-item unread">
                <i class="fas fa-envelope-open"></i>
                Belum Dibaca: {{ $unreadCount }}
            </span>
            @endif
        </div>
    </div>
    
    <div class="table-container">
        @if($messages->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Subjek</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $message)
                <tr class="{{ $message->status === 'unread' ? 'unread-row' : '' }}">
                    <td>
                        @if($message->status === 'unread')
                            <span class="status-badge status-pending">
                                <i class="fas fa-envelope"></i>
                                Baru
                            </span>
                        @elseif($message->status === 'read')
                            <span class="status-badge status-approved">
                                <i class="fas fa-envelope-open"></i>
                                Dibaca
                            </span>
                        @else
                            <span class="status-badge status-completed">
                                <i class="fas fa-reply"></i>
                                Dibalas
                            </span>
                        @endif
                    </td>
                    <td class="font-weight-600">{{ $message->name }}</td>
                    <td>{{ $message->email }}</td>
                    <td>
                        <div class="subject-preview">
                            {{ Str::limit($message->subject, 50) }}
                        </div>
                    </td>
                    <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.messages.detail', $message->id) }}" class="btn-action btn-primary" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($message->status === 'unread')
                            <form method="POST" action="{{ route('admin.messages.mark-read', $message->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-secondary" title="Tandai Sudah Dibaca">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="pagination-wrapper">
            {{ $messages->links() }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-envelope-open-text"></i>
            <h3>Belum Ada Pesan</h3>
            <p>Belum ada pesan yang masuk dari pengunjung website.</p>
        </div>
        @endif
    </div>
</div>

<style>
.unread-row {
    background: rgba(249, 115, 22, 0.05) !important;
    font-weight: 600;
}

.subject-preview {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.stat-item.unread {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.status-completed {
    background: rgba(16, 185, 129, 0.1);
    color: #065f46;
    border: 1px solid rgba(16, 185, 129, 0.2);
}
</style>
@endsection
