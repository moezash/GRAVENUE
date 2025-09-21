@extends('layouts.admin')

@section('title', isset($facility) ? 'Edit Fasilitas' : 'Tambah Fasilitas')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-{{ isset($facility) ? 'edit' : 'plus' }}"></i>
            {{ isset($facility) ? 'Edit Fasilitas' : 'Tambah Fasilitas' }}
        </h2>
        <a href="{{ route('admin.facilities') }}" class="card-action">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
    
    <div style="padding: 32px;">
        <form method="POST" action="{{ isset($facility) ? route('admin.facilities.update', $facility->id) : route('admin.facilities.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($facility))
                @method('PUT')
            @endif
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Fasilitas *</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $facility->name ?? '') }}" required>
                    @error('name')
                        <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Pilih Kategori</option>
                        <option value="Event Space" {{ old('category', $facility->category ?? '') == 'Event Space' ? 'selected' : '' }}>Event Space</option>
                        <option value="Classroom" {{ old('category', $facility->category ?? '') == 'Classroom' ? 'selected' : '' }}>Classroom</option>
                        <option value="Sports" {{ old('category', $facility->category ?? '') == 'Sports' ? 'selected' : '' }}>Sports</option>
                        <option value="Laboratory" {{ old('category', $facility->category ?? '') == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                    </select>
                    @error('category')
                        <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-textarea" rows="4">{{ old('description', $facility->description ?? '') }}</textarea>
                @error('description')
                    <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Kapasitas (orang)</label>
                    <input type="number" name="capacity" class="form-input" min="1" value="{{ old('capacity', $facility->capacity ?? '') }}">
                    @error('capacity')
                        <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Harga per Jam (Rp)</label>
                    <input type="number" name="price_per_hour" class="form-input" min="0" value="{{ old('price_per_hour', $facility->price_per_hour ?? '') }}">
                    @error('price_per_hour')
                        <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Fasilitas & Fitur</label>
                    <input type="text" name="features" class="form-input" placeholder="Contoh: AC, Proyektor, Sound System" value="{{ old('features', $facility->features ?? '') }}">
                    @error('features')
                        <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="available" {{ old('status', $facility->status ?? '') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="maintenance" {{ old('status', $facility->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="unavailable" {{ old('status', $facility->status ?? '') == 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                    @error('status')
                        <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Gambar Fasilitas</label>
                <input type="file" name="image" class="form-input" accept="image/*">
                @if(isset($facility) && $facility->image)
                    <div style="margin-top: 12px;">
                        <img src="{{ asset('storage/' . $facility->image) }}" alt="Current Image" style="max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Gambar saat ini</p>
                    </div>
                @endif
                @error('image')
                    <small style="color: #ef4444; font-size: 12px;">{{ $message }}</small>
                @enderror
            </div>
            
            <div style="display: flex; gap: 16px; margin-top: 32px;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    {{ isset($facility) ? 'Update Fasilitas' : 'Tambah Fasilitas' }}
                </button>
                
                <a href="{{ route('admin.facilities') }}" style="padding: 12px 24px; border: 2px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #64748b; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<div style="position: fixed; top: 20px; right: 20px; background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; border: 1px solid #fecaca; z-index: 1000; max-width: 400px;">
    <strong>Terjadi kesalahan:</strong>
    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection
