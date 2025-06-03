import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import Navbar from '@/components/Navbar';
import Footer from '@/Components/Footer';

export default function EventHistory({ auth, payments }) {
    payments = payments ?? [];
    const [selectedPayment, setSelectedPayment] = useState(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isPreviewOpen, setIsPreviewOpen] = useState(false);
    const [previewUrl, setPreviewUrl] = useState('');
    const [filterStatus, setFilterStatus] = useState('all');

    // Inertia form for file upload
    const { data, setData, post, processing, reset, errors } = useForm({
        payment_id: '',
        receipt: null,
    });

    const openModal = (payment) => {
        setSelectedPayment(payment);
        setData('payment_id', payment.id);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        reset();
    };

    const handleFileChange = (e) => {
        setData('receipt', e.target.files[0]);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('payment.history.confirm.store'), {
            forceFormData: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };

    const openPreview = (url) => {
        setPreviewUrl(url);
        setIsPreviewOpen(true);
    };

    const closePreview = () => {
        setIsPreviewOpen(false);
        setPreviewUrl('');
    };

    const filteredPayments = payments.filter(payment => {
        if (filterStatus === 'all') return true;
        if (filterStatus === 'pending') return payment.status === 'pending';
        return payment.status === filterStatus;
    });

    return (
        <>
            <Head title="Riwayat Pembayaran Event" />
            <div className="min-h-screen bg-gray-50">
                <Navbar auth={auth} />
                <div className="max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <h2 className="text-2xl font-bold mb-6">Riwayat Pembayaran Event Anda</h2>
                    <div className="mb-6 flex items-center gap-4">
                        <label htmlFor="filterStatus" className="font-medium">Filter Status:</label>
                        <select
                            id="filterStatus"
                            value={filterStatus}
                            onChange={e => setFilterStatus(e.target.value)}
                            className="border rounded px-2 py-1"
                        >
                            <option value="all">Semua</option>
                            <option value="paid">Lunas</option>
                            <option value="pending">Menunggu Konfirmasi/Upload Bukti</option>
                            <option value="failed">Gagal</option>
                        </select>
                    </div>
                    {filteredPayments.length === 0 ? (
                        <p className="text-gray-600">Tidak ada data pembayaran sesuai filter.</p>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {filteredPayments.map((payment) => (
                                <div
                                    key={payment.id}
                                    className="bg-white shadow-md rounded-lg p-4 cursor-pointer hover:ring-2 ring-purple-400"
                                    onClick={() => window.location.href = route('payment.history.show', { payment: payment.id })}
                                >
                                    <img
                                        src={payment.event?.thumbnail_url || '/images/no-image.png'}
                                        alt={payment.event?.title || 'Event'}
                                        className="h-40 w-full object-cover rounded"
                                    />
                                    <h3 className="mt-2 text-xl font-semibold">{payment.event?.title}</h3>
                                    <p className="text-gray-600 text-sm">
                                        {payment.event?.start_date
                                            ? new Date(payment.event.start_date).toLocaleDateString()
                                            : '-'}{' '}
                                        -{' '}
                                        {payment.event?.end_date
                                            ? new Date(payment.event.end_date).toLocaleDateString()
                                            : '-'}
                                    </p>
                                    <p className="text-gray-500 text-sm mt-1">{payment.event?.location}</p>
                                    <p className="text-gray-800 font-medium mt-2">
                                        Total Bayar: {parseInt(payment.amount || 0) === 0
                                            ? 'Gratis'
                                            : `Rp ${parseInt(payment.amount).toLocaleString('id-ID')}`}
                                    </p>
                                    <p className="text-gray-800 text-sm">
                                        Status:{' '}
                                        <span className={
                                            payment.status === 'paid'
                                                ? 'text-green-600'
                                                : payment.status === 'pending' && !payment.receipt_path
                                                    ? 'text-yellow-600'
                                                    : payment.status === 'pending' && payment.receipt_path
                                                        ? 'text-blue-600'
                                                        : 'text-red-600'
                                        }>
                                            {payment.status === 'pending' && !payment.receipt_path
                                                ? 'Upload Bukti Pembayaran'
                                                : payment.status === 'pending' && payment.receipt_path
                                                    ? 'Menunggu Konfirmasi Admin'
                                                    : payment.status === 'paid'
                                                        ? 'Lunas'
                                                        : 'Gagal'}
                                        </span>
                                    </p>
                                    <div className="mt-2 flex justify-between items-center">
                                        <p className="text-xs text-gray-400">
                                            Dibuat: {new Date(payment.created_at).toLocaleString('id-ID')}
                                        </p>
                                        {payment.status !== 'paid' && (
                                            payment.receipt_path ? (
                                                <button
                                                    onClick={() => openPreview(payment.receipt_path.startsWith('http') ? payment.receipt_path : `/storage/${payment.receipt_path}`)}
                                                    className="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                                                >
                                                    Lihat Bukti
                                                </button>
                                            ) : (
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        openModal(payment);
                                                    }}
                                                    className="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                                                >
                                                    Upload Bukti
                                                </button>
                                            )
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
                <Footer />
            </div>

            {/* Payment Confirmation Modal */}
            {isModalOpen && selectedPayment && (
                <div
                    className="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
                    onClick={closeModal}
                >
                    <div
                        className="bg-white rounded-lg w-full max-w-md p-6 relative"
                        onClick={e => e.stopPropagation()}
                    >
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-xl font-bold">Konfirmasi Pembayaran</h3>
                            <button
                                onClick={closeModal}
                                className="text-gray-500 hover:text-gray-700"
                                aria-label="Tutup"
                            >
                                ✕
                            </button>
                        </div>

                        <div className="flex justify-center mb-4">
                            <img
                                src={selectedPayment?.qr_code_url || '/images/static-qr.jpeg'}
                                alt="Kode QRIS"
                                className="w-64 h-64 object-contain"
                            />
                        </div>

                        <div className="text-center text-gray-600 mb-4">
                            <p>Acara: <strong>{selectedPayment?.event?.title}</strong></p>
                            <p>Total Bayar: <strong>Rp {parseInt(selectedPayment?.amount || 0).toLocaleString('id-ID')}</strong></p>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Upload Bukti Pembayaran
                                </label>
                                <input
                                    type="file"
                                    accept="image/*,application/pdf"
                                    onChange={handleFileChange}
                                    className="mt-1 block w-full text-sm text-gray-700"
                                />
                                {errors.receipt && (
                                    <div className="text-red-500 text-xs mt-1">{errors.receipt}</div>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing || !data.receipt}
                                className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                {processing ? 'Mengunggah…' : 'Kirim Bukti'}
                            </button>
                        </form>
                    </div>
                </div>
            )}

            {isPreviewOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onClick={closePreview}>
                    <div className="bg-white rounded-lg max-w-lg w-full p-4 relative" onClick={e => e.stopPropagation()}>
                        <button
                            onClick={closePreview}
                            className="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
                            aria-label="Tutup"
                        >✕</button>
                        <h3 className="text-lg font-bold mb-4">Preview Bukti Pembayaran</h3>
                        {previewUrl.match(/\.(jpg|jpeg|png)$/i) ? (
                            <img src={previewUrl} alt="Bukti Pembayaran" className="w-full rounded" />
                        ) : (
                            <iframe src={previewUrl} title="Bukti Pembayaran" className="w-full h-96 rounded" />
                        )}
                    </div>
                </div>
            )}
        </>
    );
}