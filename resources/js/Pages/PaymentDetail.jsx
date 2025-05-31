import { Head } from '@inertiajs/react';
import Navbar from '@/Components/Navbar';
import Footer from '@/Components/Footer'; 

function statusLabel(status) {
    if (status === 'paid') return (
        <span className="inline-flex items-center gap-1 px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-semibold">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" /></svg>
            Lunas
        </span>
    );
    if (status === 'pending') return (
        <span className="inline-flex items-center gap-1 px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs font-semibold">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3" /></svg>
            Menunggu Konfirmasi
        </span>
    );
    if (status === 'used') return (
        <span className="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs font-semibold">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3" /></svg>
            Sudah Digunakan
        </span>
    );
    return (
        <span className="inline-flex items-center gap-1 px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-semibold">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
            Gagal
        </span>
    );
}

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

export default function PaymentDetail({ auth, payment }) {
    // Gambar event jika ada
    const eventImage = payment.tickets[0]?.event?.thumbnail || null;

    return (
        <>
            <Head title={`Detail Pembayaran #${payment.id}`} />
            <Navbar auth={auth} />
            <div className="min-h-screen bg-gray-50 py-10">
                <div className="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">
                    <div className="flex justify-between items-center mb-4">
                        <button
                            onClick={() => window.history.back()}
                            className="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700 transition"
                        >
                            &larr; Kembali
                        </button>
                        
                        <a
                            href={route('user.payments.download', { payment: payment.id })}
                            className="inline-flex items-center gap-2 px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700 transition"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                            </svg>
                            Download Detail Pembayaran
                        </a>
                    </div>
                    {/* Gambar event */}
                    {eventImage && (
                        <div className="flex justify-center mb-4">
                            <img
                                src={eventImage.startsWith('http') ? eventImage : `/storage/${eventImage}`}
                                alt="Event"
                                className="h-40 rounded-xl shadow object-cover"
                            />
                        </div>
                    )}
                    <h2 className="text-2xl font-bold mb-2 text-center">Detail Pembayaran</h2>
                    <div className="mb-4 flex items-center gap-3 justify-center">
                        <span className="text-gray-500">Transaction ID:</span>
                        <span className="font-mono text-sm">{payment.transaction_id}</span>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <div className="mb-2 flex items-center gap-2">
                                <svg className="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3" /></svg>
                                <b>Metode:</b> {payment.method}
                            </div>
                            <div className="mb-2"><b>Nama:</b> {payment.buyer_name}</div>
                            <div className="mb-2"><b>Dibuat:</b> {formatDate(payment.created_at)}</div>
                            {payment.paid_at && <div className="mb-2"><b>Dibayar:</b> {formatDate(payment.paid_at)}</div>}
                        </div>
                        <div>
                            <div className="mb-2 flex items-center gap-2">
                                <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-4.418 0-8 1.79-8 4v4h16v-4c0-2.21-3.582-4-8-4z" /></svg>
                                <b>Nominal:</b> {parseInt(payment.amount) === 0 ? 'Gratis' : `Rp ${parseInt(payment.amount).toLocaleString('id-ID')}`}
                            </div>
                            <div className="mb-2"><b>Email:</b> {payment.buyer_email}</div>
                            <div className="mb-2"><b>Telepon:</b> {payment.buyer_phone}</div>
                            {statusLabel(payment.status)}
                        </div>
                    </div>
                    {payment.receipt_path && (
                        <div className="mb-6 text-center">
                            <b>Bukti Pembayaran:</b>
                            <div className="mt-2 flex justify-center">
                                <img src={`/storage/${payment.receipt_path}`} alt="Bukti" className="max-w-xs rounded border" />
                            </div>
                        </div>
                    )}
                    <hr className="my-8" />
                    <h3 className="text-xl font-bold mb-4 text-center">Tiket Terkait</h3>
                    <div className="grid grid-cols-1 gap-6">
                        {payment.tickets.map(ticket => (
                            <div
                                key={ticket.id}
                                className="bg-white border border-purple-200 rounded-2xl shadow-lg p-6 flex flex-col items-center hover:shadow-xl transition"
                            >
                                <div className="mb-3 flex flex-col items-center">
                                    <img
                                        src={ticket.qr_code_url}
                                        alt="QR"
                                        className="w-48 h-48 object-contain rounded-xl bg-gray-50 border-2 border-purple-200 shadow mb-2"
                                    />
                                    <span className="text-xs text-gray-500">QR Tiket #{ticket.id}</span>
                                </div>
                                <div className="w-full">
                                    <div className="flex justify-between items-center mb-2">
                                        <div className="font-semibold text-lg text-purple-700">{ticket.event_title}</div>
                                        <a
                                            href={route('user.tickets.download', { ticket: ticket.id })}
                                            className="inline-flex items-center gap-1 px-3 py-1 rounded bg-purple-600 text-white text-xs font-semibold shadow hover:bg-purple-700 transition"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" /></svg>
                                            Download
                                        </a>
                                    </div>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1 mb-2">
                                        <div><b>Peserta:</b> {ticket.participant_name}</div>
                                        <div><b>Email:</b> {ticket.participant_email}</div>
                                        <div><b>Telepon:</b> {ticket.participant_phone}</div>
                                        <div><b>Harga:</b> {parseInt(ticket.price_paid) === 0 ? 'Gratis' : `Rp ${parseInt(ticket.price_paid).toLocaleString('id-ID')}`}</div>
                                        <div><b>Dibuat:</b> {formatDate(ticket.created_at)}</div>
                                        <div><b>Status:</b> {statusLabel(ticket.status)}</div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            <Footer />
        </>
    );
} 