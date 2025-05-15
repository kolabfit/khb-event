// resources/js/Pages/OrderPage.jsx
import React from 'react';
import { useForm } from '@inertiajs/inertia-react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import { Inertia } from '@inertiajs/inertia';

export default function OrderPage({ event, auth }) {
    const { data, setData, post, errors, processing } = useForm({
        id: event.id,
        fullname: '',
        email: '',
        phone: '',
        quantity: 1,
        payment_method: 'qris',
    });

    // Gunakan integer mentah, fallback ke 0 jika null
    const rawPrice = event.price ?? 0;
    const total = rawPrice * data.quantity;

    const handleSubmit = (e) => {
        e.preventDefault();

        // Ambil harga mentah (number) atau 0 kalau gratis
        const rawPrice = event.price != null ? Number(event.price) : 0;

        if (rawPrice === 0) {
            // Gratis: langsung catat order dan lompat ke success
            post(route('order-event.order.store'), {
                onSuccess: () => {
                    // redirect ke dashboard atau halaman success
                    Inertia.visit(route('dashboard'), { replace: true });
                },
            });
        } else {
            // Berbayar: lempar ke confirmPayment.jsx dengan data
            post(route('order-event.order.store', { id: event.id, quantity: data.quantity }));
        }
    };

    return (
        <>
            <Navbar auth={auth} />

            <div className="bg-gray-100 min-h-screen py-12">
                <div className="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                    {/* Header Image */}
                    <img
                        src={event.thumbnail_url}
                        alt={event.title}
                        className="w-full h-64 object-cover"
                    />

                    {/* Konten Utama */}
                    <div className="p-6 space-y-6">
                        <h1 className="text-3xl font-bold">Pesan Tiket</h1>
                        <p className="text-gray-600">
                            Acara: <span className="font-medium">{event.title}</span>
                        </p>

                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Biodata */}
                            <div className="grid grid-cols-1 gap-4">
                                <div>
                                    <label className="block text-sm font-medium">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        value={data.fullname}
                                        onChange={e => setData('fullname', e.target.value)}
                                        required
                                        className="mt-1 block w-full border-gray-300 rounded-md"
                                    />
                                    {errors.fullname && (
                                        <p className="text-red-600 text-sm">{errors.fullname}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">Email</label>
                                    <input
                                        type="email"
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        required
                                        className="mt-1 block w-full border-gray-300 rounded-md"
                                    />
                                    {errors.email && (
                                        <p className="text-red-600 text-sm">{errors.email}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium">No. HP</label>
                                    <input
                                        type="tel"
                                        value={data.phone}
                                        onChange={e => setData('phone', e.target.value)}
                                        required
                                        className="mt-1 block w-full border-gray-300 rounded-md"
                                    />
                                    {errors.phone && (
                                        <p className="text-red-600 text-sm">{errors.phone}</p>
                                    )}
                                </div>
                            </div>

                            {/* Ringkasan Harga & Kuota */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-4">
                                    <div>
                                        <p className="text-sm text-gray-500">Harga/Tiket</p>
                                        <p className="text-lg font-semibold">
                                            {event.price_label.toLocaleString('id-ID')}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Kuota Tersisa</p>
                                        <p className="text-lg font-semibold">{event.quota}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Total Bayar</p>
                                        <p className="text-xl font-bold text-green-600">
                                            {rawPrice === 0
                                                ? '0'
                                                : `Rp ${total.toLocaleString('id-ID')}`}
                                        </p>
                                    </div>
                                </div>

                                {/* Pilih Jumlah & Metode Pembayaran */}
                                <div className="space-y-4">
                                    <label className="block text-sm font-medium">Jumlah Tiket</label>
                                    <div className="inline-flex items-center border border-gray-300 rounded-md overflow-hidden">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setData('quantity', Math.max(1, data.quantity - 1))
                                            }
                                            className="px-3 py-2 bg-gray-200 hover:bg-gray-300"
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
                                                    Math.min(
                                                        event.quota,
                                                        Math.max(1, parseInt(e.target.value, 10) || 1)
                                                    )
                                                )
                                            }
                                            className="w-16 text-center bg-white"
                                        />
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setData(
                                                    'quantity',
                                                    Math.min(event.quota, data.quantity + 1)
                                                )
                                            }
                                            className="px-3 py-2 bg-gray-200 hover:bg-gray-300"
                                        >
                                            +
                                        </button>
                                    </div>
                                    {errors.quantity && (
                                        <p className="text-red-600 text-sm">{errors.quantity}</p>
                                    )}

                                    <div>
                                        <label className="block text-sm font-medium">
                                            Metode Pembayaran
                                        </label>
                                        <select
                                            value={data.payment_method}
                                            disabled
                                            className="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md"
                                        >
                                            <option value="qris">QRIS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {/* Tombol Bayar */}
                            <div className="flex justify-end">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                                >
                                    {processing ? 'Memesan…' : 'Bayar'}
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
