import React from 'react';
import { Calendar, MapPin, Clock, Users } from 'lucide-react';
import Navbar from '@/components/Navbar';

export default function EventDetailPage() {
    // Contoh data—ganti dengan fetch/API call di proyek nyata
    const event = {
        id: 123,
        title: 'Konser Musik Live',
        image: 'http://127.0.1:8000/image/1.jpeg',
        date: '30 April 2025',
        time: '19:00 – 22:00',
        location: 'Stadion Utama, Jakarta',
        description: `Nikmati malam penuh musik dengan penampilan artis ternama.
      Suasana meriah, sound system profesional, dan lampu spektakuler
      siap menyambut kamu. Jangan sampai ketinggalan!`,
        organizerName: 'EO Musik Nusantara',
        organizerImage: 'http://127.0.1:8000/images/eo1.jpg',
        categories: ['Musik', 'Konser', 'Live'],
        ticketTypes: [
            { id: 1, name: 'General Admission', price: 150000, available: 120 },
            { id: 2, name: 'VIP', price: 350000, available: 30 },
        ],
    };

    return (
        <>
            <Navbar />
            <div className="container mx-auto px-4 py-8">
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
                        {/* Hero Image */}
                        <div className="overflow-hidden rounded-lg">
                            <img
                                src={event.image}
                                alt={event.title}
                                className="w-full h-64 object-cover"
                            />
                        </div>

                        {/* Title & Metadata */}
                        <div className="space-y-2">
                            <h1 className="text-3xl lg:text-4xl font-bold text-gray-800">
                                {event.title}
                            </h1>
                            <div className="flex flex-wrap items-center text-gray-600 space-x-4">
                                <span className="flex items-center">
                                    <Calendar className="w-4 h-4 mr-1" /> {event.date}
                                </span>
                                <span className="flex items-center">
                                    <Clock className="w-4 h-4 mr-1" /> {event.time}
                                </span>
                                <span className="flex items-center">
                                    <MapPin className="w-4 h-4 mr-1" /> {event.location}
                                </span>
                            </div>
                            <div className="flex flex-wrap gap-2 mt-2">
                                {event.categories.map((cat) => (
                                    <span
                                        key={cat}
                                        className="text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full"
                                    >
                                        {cat}
                                    </span>
                                ))}
                            </div>
                        </div>

                        {/* Description */}
                        <div className="prose prose-lg text-gray-700">
                            <p>{event.description}</p>
                        </div>
                    </div>

                    {/* Right: Panel Pembelian */}
                    <aside className="lg:w-1/3 space-y-6">
                        {/* Organizer */}
                        <div className="flex items-center space-x-4 p-4 bg-white rounded-lg shadow-sm">
                            <img
                                src={event.organizerImage}
                                alt={event.organizerName}
                                className="w-12 h-12 rounded-full object-cover"
                            />
                            <div>
                                <p className="text-sm text-gray-500">Diselenggarakan oleh</p>
                                <p className="font-medium text-gray-800">{event.organizerName}</p>
                            </div>
                        </div>

                        {/* Ticket Types */}
                        <div className="bg-white rounded-lg shadow-sm p-4 space-y-4">
                            <h2 className="text-lg font-semibold text-gray-800">Pilih Tiket</h2>
                            {event.ticketTypes.map((type) => (
                                <div
                                    key={type.id}
                                    className="flex items-center justify-between border-b last:border-b-0 pb-3"
                                >
                                    <div>
                                        <p className="font-medium text-gray-800">{type.name}</p>
                                        <p className="text-sm text-gray-500">
                                            {type.available} tersisa
                                        </p>
                                    </div>
                                    <div className="text-right">
                                        <p className="font-semibold text-gray-800">
                                            Rp {type.price.toLocaleString('id-ID')}
                                        </p>
                                        <button
                                            className="mt-2 px-4 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition"
                                            onClick={() =>
                                                console.log(`Pesan ${type.name} untuk event ${event.title}`)
                                            }
                                        >
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            ))}
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
        </>
    );
}
