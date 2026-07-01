@extends('layouts.app')

@section('title', 'Kelola Anggota')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold">Kelola Anggota</h1>
        <p class="text-gray-500 text-sm">Manajemen data anggota perpustakaan</p>
    </div>
    <button onclick="openModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
        + Tambah Anggota
    </button>
</div>

<!-- Search -->
<input id="search" type="text" placeholder="Cari NIM, nama, atau email..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Tabel Anggota -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-600">NIM</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Nama</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Email</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Prodi</th>
                    <th class="text-left p-4 font-semibold text-gray-600">No. Telepon</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Status</th>
                    <th class="text-center p-4 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody id="anggotaTable">
                <!-- Data akan diisi JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Anggota -->
<div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h2 id="modalTitle" class="text-lg font-bold">Tambah Anggota</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
            <form id="anggotaForm">
                <input type="hidden" id="anggotaId" name="anggota_id">
                
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">NIM <span class="text-red-500">*</span></label>
                    <input id="nim" type="text" placeholder="Contoh: 20230001" class="w-full p-2 border rounded-lg" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input id="name" type="text" placeholder="Nama lengkap" class="w-full p-2 border rounded-lg" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                    <input id="email" type="email" placeholder="contoh@email.com" class="w-full p-2 border rounded-lg" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Program Studi</label>
                    <select id="prodi" class="w-full p-2 border rounded-lg">
                        <option value="">Pilih Program Studi</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Teknologi Geomatika">Teknologi Geomatika</option>
                        <option value="Terapan Animasi">Terapan Animasi</option>
                        <option value="Teknologi Rekayasa Multimedia">Teknologi Rekayasa Multimedia</option>
                        <option value="Teknik Komputer">Teknik Komputer</option>
                        <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                        <option value="Rekayasa Keamanan Siber">Rekayasa Keamanan Siber</option>
                        <option value="Teknologi Permainan">Teknologi Permainan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">No. Telepon</label>
                    <input id="phone" type="text" placeholder="Contoh: 08123456789" class="w-full p-2 border rounded-lg">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Password <span class="text-red-500">*</span></label>
                    <input id="password" type="password" placeholder="Minimal 6 karakter" class="w-full p-2 border rounded-lg" required>
                    <p id="passwordHint" class="text-xs text-gray-400 mt-1 hidden">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-lg w-full hover:bg-indigo-600 transition">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg w-full hover:bg-gray-500 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let anggota = [];
let editId = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Load data anggota dari database
async function loadAnggota() {
    try {
        const response = await fetch("{{ url('api/anggota') }}");
        anggota = await response.json();
        renderAnggota();
    } catch (error) {
        console.error('Gagal load anggota:', error);
    }
}

function renderAnggota() {
    const search = document.getElementById('search').value.toLowerCase();
    const tbody = document.getElementById('anggotaTable');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    const filtered = anggota.filter(a => 
        a.nim.toLowerCase().includes(search) || 
        a.name.toLowerCase().includes(search) ||
        (a.email && a.email.toLowerCase().includes(search))
    );

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-gray-500 py-10">📭 Tidak ada data anggota. Silakan tambah anggota baru.</td></tr>`;
        return;
    }

    filtered.forEach(angg => {
        const statusClass = angg.status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        const statusText = angg.status === 'aktif' ? 'Aktif' : 'Nonaktif';
        
        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 font-mono text-sm">${escapeHtml(angg.nim)}</td>
                <td class="p-4 font-medium">${escapeHtml(angg.name)}</td>
                <td class="p-4 text-gray-600">${escapeHtml(angg.email)}</td>
                <td class="p-4 text-gray-600">${escapeHtml(angg.prodi) || '-'}</td>
                <td class="p-4 text-gray-600">${escapeHtml(angg.phone) || '-'}</td>
                <td class="p-4">
                    <button onclick="toggleStatus(${angg.id})" class="px-2 py-1 rounded-full text-xs ${statusClass} cursor-pointer hover:opacity-80">
                        ${statusText}
                    </button>
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                    <button onclick="editAnggota(${angg.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs mr-1">
                        Edit
                    </button>
                    <button onclick="deleteAnggota(${angg.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                        Hapus
                    </button>
                </td>
            </tr>
        `;
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function openModal() {
    editId = null;
    document.getElementById('anggotaId').value = '';
    document.getElementById('nim').value = '';
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('prodi').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passwordHint').classList.add('hidden');
    document.getElementById('modalTitle').innerText = 'Tambah Anggota';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Submit form
document.getElementById('anggotaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const nim = document.getElementById('nim').value.trim();
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const prodi = document.getElementById('prodi').value;
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;

    if (!nim || !name || !email) {
        alert('NIM, Nama, dan Email wajib diisi!');
        return;
    }

    if (!editId && (!password || password.length < 6)) {
        alert('Password minimal 6 karakter!');
        return;
    }

    let url, method, body;

    if (editId) {
        url = "{{ url('admin/anggota') }}/" + editId;
        method = 'PUT';
        body = { nim, name, email, prodi, phone, password, _method: 'PUT' };
    } else {
        url = "{{ url('admin/anggota') }}";
        method = 'POST';
        body = { nim, name, email, prodi, phone, password };
    }

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(body)
        });

        const result = await response.json();

        if (response.ok) {
            alert(editId ? '✅ Anggota berhasil diupdate!' : '✅ Anggota berhasil ditambahkan!');
            closeModal();
            loadAnggota();
        } else {
            alert('❌ Gagal menyimpan anggota: ' + (result.message || 'Terjadi kesalahan'));
        }
    } catch (error) {
        alert('❌ Terjadi kesalahan: ' + error.message);
    }
});

async function editAnggota(id) {
    try {
        const response = await fetch("{{ url('admin/anggota') }}/" + id + "/edit");
        const angg = await response.json();

        document.getElementById('anggotaId').value = angg.id;
        document.getElementById('nim').value = angg.nim;
        document.getElementById('name').value = angg.name;
        document.getElementById('email').value = angg.email;
        document.getElementById('prodi').value = angg.prodi || '';
        document.getElementById('phone').value = angg.phone || '';
        document.getElementById('password').value = '';
        document.getElementById('password').required = false;
        document.getElementById('passwordHint').classList.remove('hidden');

        editId = id;
        document.getElementById('modalTitle').innerText = 'Edit Anggota';
        document.getElementById('modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        alert('Gagal mengambil data anggota');
    }
}

async function deleteAnggota(id) {
    const angg = anggota.find(a => a.id === id);
    if (confirm(`Yakin ingin menghapus anggota "${angg?.name}"?`)) {
        try {
            const response = await fetch("{{ url('admin/anggota') }}/" + id, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                alert('🗑️ Anggota berhasil dihapus!');
                loadAnggota();
            } else {
                alert('❌ Gagal menghapus anggota');
            }
        } catch (error) {
            alert('❌ Terjadi kesalahan: ' + error.message);
        }
    }
}

async function toggleStatus(id) {
    try {
        const response = await fetch("{{ url('admin/anggota') }}/" + id + "/toggle-status", {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        if (response.ok) {
            loadAnggota();
        } else {
            alert('❌ Gagal mengubah status');
        }
    } catch (error) {
        alert('❌ Terjadi kesalahan: ' + error.message);
    }
}

// Search event
document.getElementById('search').addEventListener('input', renderAnggota);

// Load data awal
loadAnggota();
</script>

@endsection