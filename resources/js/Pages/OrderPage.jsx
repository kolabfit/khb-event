import React, { useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import { Inertia } from '@inertiajs/inertia';
import { Calendar, MapPin, Clock } from 'lucide-react'; // Import icons

export default function OrderPage({ event, auth }) {
    const { data, setData, post, errors, processing } = useForm({
        id: event.id,
        quantity: 1,
        payment_method: 'qris',
        addSelfAsParticipant: false,
        participants: [{ fullname: '', email: '', phone: '' }],
    });

    // Tampilkan informasi event untuk debugging
    useEffect(() => {
        console.log("Data event yang diterima:", event);
    }, []);

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

    // Handle kembali ke halaman detail event
    const handleBack = () => {
    const url = route('detail-events') + `?id=${event.id}`;
    Inertia.visit(url, { preserveState: true });
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
                        <div className="space-y-2">
                            <h1 className="text-3xl font-bold">Pesan Tiket</h1>
                            <p className="text-gray-600">
                                Acara: <span className="font-medium">{event.title}</span>
                            </p>
                            
                            {/* Tambahkan waktu dan lokasi event */}
                            <div className="flex flex-wrap items-center text-gray-600 space-x-4 mt-2">
                                <span className="flex items-center">
                                    <Calendar className="w-4 h-4 mr-1" />
                                    {event.start_date ? new Date(event.start_date).toLocaleDateString("id-ID", {
                                        day: "numeric",
                                        month: "long",
                                        year: "numeric",
                                    }) : "Tanggal belum ditentukan"}
                                </span>
                                <span className="flex items-center">
                                    <Clock className="w-4 h-4 mr-1" />
                                    {event.start_date ? new Date(event.start_date).toLocaleTimeString("id-ID", {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    }) : "00:00"}{" "}
                                    -{" "}
                                    {event.end_date ? new Date(event.end_date).toLocaleTimeString("id-ID", {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    }) : "00:00"}
                                </span>
                            </div>
                            <div className="text-gray-600">
                                <span className="flex items-center">
                                    <MapPin className="w-4 h-4 mr-1" /> {event.location || "Lokasi belum ditentukan"}
                                </span>
                            </div>
                        </div>

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

                            {/* Tombol - Tambahkan tombol Kembali di sebelah kiri tombol Bayar */}
                            <div className="flex justify-end space-x-4">
                                <button
                                    type="button"
                                    onClick={handleBack}
                                    className="px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                                >
                                    Kembali
                                </button>
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