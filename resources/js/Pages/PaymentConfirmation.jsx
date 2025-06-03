import React from 'react';
import { useForm } from '@inertiajs/inertia-react';
import Navbar from '@/components/Navbar';
import Footer from '@/Components/Footer';

export default function PaymentConfirmation({ ticket, auth, qris }) {
    const { data, setData, post, processing, errors } = useForm({
        ticket_id: ticket.id,
        receipt: null,
    });

    const handleUpload = (e) => {
        e.preventDefault();
        post(route('payments.confirm.store'), {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    // Pastikan kita punya angka, fallback 0
    const pricePerTicket = Number(ticket.price_per_ticket) || 0;
    const totalPaid = Number(ticket.total_paid) || 0;

    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    });

    return (
        <>
            <Navbar auth={auth} />

            <div className="container mx-auto px-4 py-8 sm:py-12">
                <div className="max-w-lg mx-auto bg-white rounded-lg shadow-md p-4 sm:p-6 space-y-6">
                    <h1 className="text-xl sm:text-2xl font-bold text-center">Konfirmasi Pembayaran</h1>

                    {/* QRIS */}
                    <div className="flex flex-col items-center space-y-4">
                        {qris ? (
                            <>
                                <div className="w-48 h-48 sm:w-64 sm:h-64">
                                    <img
                                        src={qris.qris_image_path}
                                        alt="Kode QRIS"
                                        className="w-full h-full object-contain"
                                    />
                                </div>
                                <div className="text-center text-sm text-gray-600">
                                    <p>Merchant: {qris.merchant_name}</p>
                                </div>
                            </>
                        ) : (
                            <div className="text-center text-red-600">
                                QRIS tidak tersedia saat ini. Silakan hubungi admin.
                            </div>
                        )}
                    </div>

                    {/* Detail */}
                    <div className="space-y-2 text-center text-gray-600 text-sm sm:text-base">
                        <p>
                            Acara: <strong>{ticket.event_title}</strong>
                        </p>
                        <p>
                            Harga/Tiket:{' '}
                            <strong>{formatter.format(pricePerTicket)}</strong>
                        </p>
                        <p>
                            Jumlah Tiket:{' '}
                            <strong>{ticket.quantity}</strong>
                        </p>
                        <p>
                            Total Bayar:{' '}
                            <strong className="text-green-600">
                                {formatter.format(totalPaid)}
                            </strong>
                        </p>
                    </div>

                    <form onSubmit={handleUpload} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Upload Bukti Pembayaran
                            </label>
                            <div className="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div className="space-y-1 text-center">
                                    <input
                                        type="file"
                                        accept="image/*,application/pdf"
                                        onChange={e => setData('receipt', e.target.files[0])}
                                        className="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    />
                                    <p className="text-xs text-gray-500">
                                        PNG, JPG, PDF up to 2MB
                                    </p>
                                </div>
                            </div>
                            {errors.receipt && (
                                <p className="text-red-600 text-sm mt-1">{errors.receipt}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50 text-sm sm:text-base"
                        >
                            {processing ? 'Mengunggah…' : 'Kirim Bukti'}
                        </button>
                    </form>
                </div>
            </div>

            <Footer />
        </>
    );
}
