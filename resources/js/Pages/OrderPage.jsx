// resources/js/Pages/OrderPage.jsx
import React, { useEffect } from 'react';
import { useForm } from '@inertiajs/inertia-react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';

export default function OrderPage({ event, auth }) {
    const { data, setData, post, errors, processing } = useForm({
        id: event.id,
        quantity: 1,
        payment_method: 'qris',
        addSelfAsParticipant: false,
        participants: [{ fullname: '', email: '', phone: '' }],
    });

    // REBUILD participants array whenever quantity atau toggle berubah
    useEffect(() => {
        const qty = data.quantity;
        // potong/preserve yang sudah ada
        const arr = data.participants.slice(0, qty);
        // extend jika kurang
        while (arr.length < qty) {
            arr.push({ fullname: '', email: '', phone: '' });
        }
        // autofill slot pertama kalau toggle on
        if (data.addSelfAsParticipant) {
            arr[0] = {
                fullname: auth.user.name,
                email: auth.user.email,
                phone: auth.user.phone || '',
            };
        } else {
            arr[0] = { fullname: '', email: '', phone: '' };
        }
        setData('participants', arr);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [data.quantity, data.addSelfAsParticipant]);

    // cuma update satu field peserta ke-i
    const handleChangeParticipant = (idx, field, value) => {
        // clone peserta lama
        const arr = data.participants.map((p, i) =>
            i === idx
                ? { ...p, [field]: value }   // update hanya index yg diketik
                : p
        );
        setData('participants', arr);
    };

    const handleSubmit = e => {
        e.preventDefault();
        post(route('order-event.order.store'));
    };

    return (
        <>
            <Navbar auth={auth} />

            <div className="bg-gray-100 min-h-screen py-12">
                <div className="max-w-3xl mx-auto bg-white rounded-lg shadow overflow-hidden">
                    <img
                        src={event.thumbnail_url}
                        alt={event.title}
                        className="w-full h-64 object-cover"
                    />

                    <div className="p-6 space-y-6">
                        <h1 className="text-3xl font-bold">Pesan Tiket</h1>
                        <p>Acara: <strong>{event.title}</strong></p>

                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* 1) Jumlah Tiket */}
                            <div className="flex flex-col md:flex-row md:justify-between md:items-center bg-white p-4 rounded-lg shadow-sm space-y-4 md:space-y-0">
                                {/* -- Info Harga -- */}
                                <div className="flex flex-col sm:flex-row sm:space-x-12 space-y-2 sm:space-y-0">
                                    <div>
                                        <span className="block text-sm text-gray-500">Harga/Tiket</span>
                                        <span className="block text-xl font-semibold text-gray-800">
                                            {event.price === 0
                                                ? 'Gratis'
                                                : `Rp ${Number(event.price).toLocaleString('id-ID')}`}
                                        </span>
                                    </div>
                                    <div>
                                        <span className="block text-sm text-gray-500">Total Harga</span>
                                        <span className="block text-xl font-semibold text-green-600">
                                            {event.price === 0
                                                ? '0'
                                                : `Rp ${(Number(event.price) * data.quantity).toLocaleString('id-ID')}`}
                                        </span>
                                    </div>
                                </div>

                                {/* -- Controls Jumlah Tiket -- */}
                                <div className="flex items-center space-x-3">
                                    <button
                                        type="button"
                                        onClick={() => setData('quantity', Math.max(1, data.quantity - 1))}
                                        className="h-10 w-10 flex items-center justify-center bg-gray-100 rounded-md hover:bg-gray-200"
                                    >
                                        −
                                    </button>
                                    <input
                                        type="number"
                                        min="1"
                                        max={event.quota}
                                        value={data.quantity}
                                        onChange={e =>
                                            setData(
                                                'quantity',
                                                Math.min(event.quota, Math.max(1, parseInt(e.target.value, 10) || 1))
                                            )
                                        }
                                        className="h-10 w-16 text-center border border-gray-300 rounded-md focus:ring-2 focus:ring-green-300"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setData('quantity', Math.min(event.quota, data.quantity + 1))}
                                        className="h-10 w-10 flex items-center justify-center bg-gray-100 rounded-md hover:bg-gray-200"
                                    >
                                        +
                                    </button>
                                    <span className="text-sm text-gray-500">
                                        Kuota tersisa: <span className="font-medium text-gray-700">{event.quota}</span>
                                    </span>
                                </div>
                            </div>

                            {/* 2) Data Pemesan */}
                            <div className="p-4 border rounded bg-gray-50 space-y-1">
                                <h2 className="font-semibold">Data Pemesan</h2>
                                <div className="flex"><span className="w-24 font-medium">Nama:</span><span>{auth.user.name}</span></div>
                                <div className="flex"><span className="w-24 font-medium">Email:</span><span>{auth.user.email}</span></div>
                                <div className="flex"><span className="w-24 font-medium">No. HP:</span><span>{auth.user.phone || '-'}</span></div>
                            </div>

                            {/* 3) Toggle Autofill */}
                            <div className="flex items-center space-x-3">
                                <span className="font-medium">Tambahkan sebagai peserta (Anda sendiri)</span>
                                <label htmlFor="add-self-toggle" className="relative inline-flex items-center cursor-pointer">
                                    <input
                                        id="add-self-toggle"
                                        type="checkbox"
                                        className="sr-only peer"
                                        checked={data.addSelfAsParticipant}
                                        onChange={e => setData('addSelfAsParticipant', e.target.checked)}
                                    />
                                    <div className="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-green-300 rounded-full peer-checked:bg-green-600 transition-colors"></div>
                                    <div className="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5"></div>
                                </label>
                            </div>

                            {/* 4) Form Biodata Peserta */}
                            <div className="space-y-4">
                                <h2 className="font-semibold">Detail Peserta</h2>
                                {data.participants.map((p, idx) => (
                                    <div key={idx} className="p-4 border rounded space-y-3">
                                        <p className="text-sm font-medium text-gray-600">Peserta {idx + 1}</p>

                                        {['fullname', 'email', 'phone'].map(field => (
                                            <div key={field}>
                                                <label className="block text-sm">
                                                    {field === 'fullname' ? 'Nama Lengkap' : field === 'email' ? 'Email' : 'No. HP'}
                                                </label>
                                                <input
                                                    type={field === 'email' ? 'email' : field === 'phone' ? 'tel' : 'text'}
                                                    value={p[field]}
                                                    onChange={e => handleChangeParticipant(idx, field, e.target.value)}
                                                    required
                                                    className="mt-1 w-full border-gray-300 rounded"
                                                />
                                                {errors[`participants.${idx}.${field}`] && (
                                                    <p className="text-red-600 text-sm">{errors[`participants.${idx}.${field}`]}</p>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                ))}
                            </div>

                            {/* 5) Submit */}
                            <div className="flex justify-end">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                                >
                                    {processing ? 'Memproses…' : 'Lanjutkan ke Pembayaran'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <Footer />
        </>
    );
}
