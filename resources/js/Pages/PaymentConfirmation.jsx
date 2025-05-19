import React from 'react';
import { useForm } from '@inertiajs/inertia-react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';

export default function PaymentConfirmation({ ticket, auth }) {
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

            <div className="container mx-auto px-4 py-12">
                <div className="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6 space-y-6">
                    <h1 className="text-2xl font-bold text-center">Konfirmasi Pembayaran</h1>

                    {/* QRIS */}
                    <div className="flex justify-center">
                        <img
                            src="/images/static-qr.jpeg"
                            alt="Kode QRIS"
                            className="w-64 h-64 object-contain"
                        />
                    </div>

                    {/* Detail */}
                    <div className="space-y-2 text-center text-gray-600">
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

                    <form onSubmit={handleUpload} encodeType="multipart/form-data" className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Upload Bukti Pembayaran
                            </label>
                            <input
                                type="file"
                                accept="image/*,application/pdf"
                                onChange={e => setData('receipt', e.target.files[0])}
                                className="mt-1 block w-full text-sm text-gray-700"
                            />
                            {errors.receipt && (
                                <p className="text-red-600 text-sm mt-1">{errors.receipt}</p>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
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
