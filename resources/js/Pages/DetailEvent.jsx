import React, { useState } from 'react';
import { Calendar, MapPin, Clock, Users } from 'lucide-react';
import Navbar from '@/components/Navbar';
import { Link } from '@inertiajs/inertia-react';
import Footer from '@/components/Footer';

export default function EventDetailPage({ event, auth }) {
    // const [quantity, setQuantity] = useState(1);
    // const increase = () => setQuantity(q => Math.min(event.quota, q + 1));
    // const decrease = () => setQuantity(q => Math.max(1, q - 1));

    return (
        <>
            <Navbar auth={auth}/>
            <div className="container mx-auto px-4 py-8 mb-12">
                {/* Breadcrumb */}
                <nav className="text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
                    <ol className="inline-flex items-center space-x-2">
                        <li><a href="/" className="hover:underline">Beranda</a></li>
                        <li>›</li>
                        <li><a href="/events" className="hover:underline">Events</a></li>
                        <li>›</li>
                        <li className="text-gray-800 font-medium">{event.title}</li>
                    </ol>
                </nav>

                <div className="flex flex-col lg:flex-row gap-8">
                    {/* Left: Detail Utama */}
                    <div className="lg:w-2/3 space-y-6">
                        <div className="overflow-hidden rounded-lg">
                            <img
                                src={event.thumbnail}
                                alt={event.title}
                                className="w-full h-64 object-cover"
                            />
                        </div>

                        <div className="space-y-2">
                            <h1 className="text-3xl lg:text-4xl font-bold text-gray-800">
                                {event.title}
                            </h1>
                            <div className="flex flex-wrap items-center text-gray-600 space-x-4">
                                <span className="flex items-center">
                                    <Calendar className="w-4 h-4 mr-1" />
                                    {new Date(event.start_date).toLocaleDateString('id-ID', {
                                        day: 'numeric', month: 'long', year: 'numeric'
                                    })}
                                </span>
                                <span className="flex items-center">
                                    <Clock className="w-4 h-4 mr-1" />
                                    {new Date(event.start_date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                    {' - '}
                                    {new Date(event.end_date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                </span>
                                <span className="flex items-center">
                                    <MapPin className="w-4 h-4 mr-1" /> {event.location}
                                </span>
                            </div>
                            <div className="flex flex-wrap gap-2 mt-2">
                                {event.categories.map(cat => (
                                    <span
                                        key={cat.name}
                                        className="text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full"
                                    >
                                        {cat.name}
                                    </span>
                                ))}
                            </div>
                        </div>

                        <div
                            className="prose prose-lg text-gray-700"
                            dangerouslySetInnerHTML={{ __html: event.description }}
                        />
                    </div>

                    {/* Right: Panel Pembelian */}
                    <aside className="lg:w-1/3 space-y-6">
                        {/* Organizer */}
                        <div className="flex items-center space-x-4 p-4 bg-white rounded-lg shadow-sm">
                            <img
                                src={event.user.avatar}
                                alt={event.user.name}
                                className="w-12 h-12 rounded-full object-cover"
                            />
                            <div>
                                <p className="text-sm text-gray-500">Diselenggarakan oleh</p>
                                <p className="font-medium text-gray-800">{event.user.name}</p>
                            </div>
                        </div>

                        {/* Ticket Types */}
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <h2 className="text-lg font-semibold text-gray-800 mb-4">Pilih Tiket</h2>
                            {/* Baris Kuota & Harga */}
                            <div className="flex items-baseline justify-between">
                                <div>
                                    <span className="text-lg font-medium text-gray-800">{event.quota} kursi</span>
                                    <p className="text-sm text-gray-500">tersisa</p>
                                </div>
                                <span className="text-lg font-semibold text-gray-800">
                                    Rp {event.price.toLocaleString('id-ID')}
                                </span>
                            </div>
                            {/* Baris Quantity & Pesan */}
                            <div className="flex items-center space-x-2 mt-4 justify-end">
                                {/* <button
                                    onClick={decrease}
                                    className="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                                >
                                    –
                                </button>
                                <input
                                    type="number"
                                    value={quantity}
                                    onChange={e => {
                                        const val = parseInt(e.target.value, 10) || 1;
                                        setQuantity(Math.min(Math.max(1, val), event.quota));
                                    }}
                                    min="1"
                                    max={event.quota}
                                    className="w-12 text-center border rounded"
                                />
                                <button
                                    onClick={increase}
                                    className="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 transition"
                                >
                                    +
                                </button> */}
                                {/* <button
                                    onClick={handleOrder}
                                    className="ml-auto px-4 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition"
                                >
                                    Pesan
                                </button> */}
                                {/* Link langsung sebagai button */}
                                <Link
                                    as="button"
                                    href={route('order-event.order', {
                                        id: event.id
                                        // kalau mau kirim quantity via query juga
                                    })}
                                    className="ml-2 px-4 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition"
                                >
                                    Pesan
                                </Link>
                            </div>
                        </div>

                        {/* Share Section */}
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <p className="text-sm text-gray-600 mb-2">Bagikan:</p>
                            <div className="flex space-x-3">
                                <a href="#" className="text-blue-600 hover:text-blue-800">Facebook</a>
                                <a href="#" className="text-blue-400 hover:text-blue-600">Twitter</a>
                                <a href="#" className="text-pink-500 hover:text-pink-700">Instagram</a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
            <Footer />
        </>
    );
}