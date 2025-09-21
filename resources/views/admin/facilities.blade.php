@extends('layouts.admin')

@section('title', 'Kelola Fasilitas')

@section('content')
<!-- Add Facility Form -->
<div class="content-card" style="margin-bottom: 32px;">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-plus"></i>
            Tambah Fasilitas
        </h2>
    </div>
    
    <div style="padding: 24px;">
        <form method="POST" action="{{ route('admin.facilities.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Fasilitas</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Pilih Kategori</option>
                        <option value="Event Space">Event Space</option>
                        <option value="Classroom">Classroom</option>
                        <option value="Sports">Sports</option>
                        <option value="Laboratory">Laboratory</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-textarea"></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Kapasitas (orang)</label>
                    <input type="number" name="capacity" class="form-input" min="1">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Harga per Jam (Rp)</label>
                    <input type="number" name="price_per_hour" class="form-input" min="0">
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Fasilitas & Fitur</label>
                    <input type="text" name="features" class="form-input" placeholder="Contoh: AC, Proyektor, Sound System">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="available">Tersedia</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="unavailable">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Gambar Fasilitas</label>
                <input type="file" name="image" class="form-input" accept="image/*">
            </div>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i>
                Tambah Fasilitas
            </button>
        </form>
    </div>
</div>

<!-- Facilities List -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Daftar Fasilitas</h2>
    </div>
    
    <!-- Search and Filter -->
    <div class="search-filter-bar" style="padding: 0 24px; margin-top: 20px;">
        <form method="GET" action="{{ route('admin.facilities') }}" style="display: flex; gap: 16px; align-items: center; width: 100%;">
            <select name="status" class="filter-select">
                <option value="all">Semua Status</option>
                <option value="available">Available</option>
                <option value="maintenance">Maintenance</option>
                <option value="unavailable">Unavailable</option>
            </select>
            <input type="text" name="search" class="search-input" placeholder="Cari Booking..." value="{{ request('search') }}">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Kapasitas</th>
                    <th>Harga/hari</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $facility)
                <tr>
                    <td>#{{ $facility->id }}</td>
                    <td>
                        <strong>{{ $facility->name }}</strong><br>
                        <small style="color: #64748b;">{{ Str::limit($facility->description, 50) }}</small>
                    </td>
                    <td>
                        <span class="status-badge status-approved">{{ $facility->category }}</span>
                    </td>
                    <td>{{ $facility->capacity }} orang</td>
                    <td>Rp {{ number_format($facility->price_per_hour, 0, ',', '.') }}</td>
                    <td>
                        @if($facility->status == 'available')
                            <span class="status-badge status-available">Available</span>
                        @elseif($facility->status == 'maintenance')
                            <span class="status-badge status-maintenance">Maintenance</span>
                        @else
                            <span class="status-badge status-unavailable">Unavailable</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-view" onclick="viewFacility({{ $facility->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn btn-edit" onclick="window.location.href='{{ route('admin.facilities.edit', $facility->id) }}'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.facilities.delete', $facility->id) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete" onclick="return confirm('Hapus fasilitas ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-building" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <br>
                        Tidak ada fasilitas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for viewing facility details -->
<div id="facilityModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 12px; padding: 24px; max-width: 500px; width: 90%;">
        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Detail Fasilitas</h3>
            <button onclick="closeFacilityModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <div id="facilityDetails"></div>
    </div>
</div>

@push('scripts')
<script>
function viewFacility(id) {
    // Simple modal implementation - in real app, you'd fetch data via AJAX
    const facilities = @json($facilities);
    const facility = facilities.find(f => f.id === id);
    
    if (facility) {
        document.getElementById('facilityDetails').innerHTML = `
            <div style="margin-bottom: 16px;">
                <strong>Nama:</strong> ${facility.name}
            </div>
            <div style="margin-bottom: 16px;">
                <strong>Kategori:</strong> ${facility.category || 'Tidak ada'}
            </div>
            <div style="margin-bottom: 16px;">
                <strong>Deskripsi:</strong> ${facility.description || 'Tidak ada deskripsi'}
            </div>
            <div style="margin-bottom: 16px;">
                <strong>Kapasitas:</strong> ${facility.capacity} orang
            </div>
            <div style="margin-bottom: 16px;">
                <strong>Harga per Jam:</strong> Rp ${new Intl.NumberFormat('id-ID').format(facility.price_per_hour)}
            </div>
            <div style="margin-bottom: 16px;">
                <strong>Fitur:</strong> ${facility.features || 'Tidak ada'}
            </div>
            <div style="margin-bottom: 16px;">
                <strong>Status:</strong> ${facility.status}
            </div>
        `;
        document.getElementById('facilityModal').style.display = 'block';
    }
}

function closeFacilityModal() {
    document.getElementById('facilityModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('facilityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFacilityModal();
    }
});
</script>
@endpush
@endsection
