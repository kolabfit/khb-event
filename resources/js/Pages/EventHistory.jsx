import { useState } from 'react';
import { Head } from '@inertiajs/react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';

export default function EventHistory({ auth, events }) {
    const [selectedEvent, setSelectedEvent] = useState(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedFile, setSelectedFile] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    
    const today = new Date();

    const openModal = (event) => {
        setSelectedEvent(event);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setSelectedFile(null);
    };

    const handleFileChange = (e) => {
        setSelectedFile(e.target.files[0]);
    };

    const handleSubmit = () => {
        setIsSubmitting(true);
        
        // Simulate submission - in a real app, you would send to server
        setTimeout(() => {
            setIsSubmitting(false);
            closeModal();
            // You could add some success feedback here
        }, 1500);
    };

    return (
        <>
            <Head title="Riwayat Event" />
            <div className="min-h-screen bg-gray-50">
                <Navbar auth={auth} />
                <div className="max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <h2 className="text-2xl font-bold mb-6">Riwayat Event Anda</h2>
                    {events.length === 0 ? (
                        <p className="text-gray-600">Anda belum memiliki riwayat event.</p>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {events.map((event) => {
                                const startDate = new Date(event.start_date);
                                const status = startDate > today ? 'Belum Dimulai' : 'Selesai';
                                const statusColor = startDate > today ? 'text-green-600' : 'text-gray-500';
                                // Assuming you have a payment_status field in your event object
                                const isPaid = event.payment_status === 'paid';

                                return (
                                    <div key={event.id} className="bg-white shadow-md rounded-lg p-4">
                                        <img
                                            src={event.thumbnail_url}
                                            alt={event.title}
                                            className="h-40 w-full object-cover rounded"
                                        />
                                        <h3 className="mt-2 text-xl font-semibold">{event.title}</h3>
                                        <p className="text-gray-600 text-sm">
                                            {startDate.toLocaleDateString()} - {new Date(event.end_date).toLocaleDateString()}
                                        </p>
                                        <p className="text-gray-500 text-sm mt-1">{event.location}</p>
                                        <p className="text-gray-800 font-medium mt-2">
                                            Harga: {event.price_label}
                                        </p>
                                        <p className="text-gray-800 text-sm">
                                            Tiket dipesan: {event.quantity}
                                        </p>
                                        <div className="mt-2 flex justify-between items-center">
                                            <p className={`font-semibold ${statusColor}`}>
                                                Status: {status}
                                            </p>
                                            {!isPaid && (
                                                <button
                                                    onClick={() => openModal(event)}
                                                    className="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                                                >
                                                    Upload Bukti
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
                <Footer />
            </div>

            {/* Payment Confirmation Modal */}
            {isModalOpen && selectedEvent && (
                <div className="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg w-full max-w-md p-6 relative">
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-xl font-bold">Konfirmasi Pembayaran</h3>
                            <button 
                                onClick={closeModal}
                                className="text-gray-500 hover:text-gray-700"
                            >
                                ✕
                            </button>
                        </div>

                        <div className="flex justify-center mb-4">
                            <img
                                src="/images/static-qr.jpeg"
                                alt="Kode QRIS"
                                className="w-64 h-64 object-contain"
                            />
                        </div>

                        <div className="text-center text-gray-600 mb-4">
                            <p>Acara: <strong>{selectedEvent?.title}</strong></p>
                            <p>Jumlah Tiket: <strong>{selectedEvent?.quantity}</strong></p>
                            <p>Total Bayar: <strong>Rp {parseInt(selectedEvent?.price || 0).toLocaleString('id-ID')}</strong></p>
                        </div>

                        <div className="space-y-4">
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
                            </div>

                            <button
                                onClick={handleSubmit}
                                disabled={isSubmitting || !selectedFile}
                                className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                {isSubmitting ? 'Mengunggah…' : 'Kirim Bukti'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}