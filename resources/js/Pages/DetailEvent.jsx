// resources/js/Pages/EventDetailPage.jsx
import React from "react";
import { Calendar, MapPin, Clock } from "lucide-react";
import Navbar from "@/components/Navbar";
import { Link } from "@inertiajs/react";
import Footer from "@/Components/Footer";

export default function EventDetailPage({ event, auth }) {
    // Hitung harga mentah, default ke 0 bila null
    const rawPrice = event.price != null ? Number(event.price) : 0;
    // Siapkan label: rupiah bila >0, "Gratis" bila 0
    const priceLabel =
        rawPrice > 0
            ? rawPrice.toLocaleString("id-ID", {
                  style: "currency",
                  currency: "IDR",
                  minimumFractionDigits: 0,
              })
            : "Gratis";

    return (
        <>
            <Navbar auth={auth} />
            <div className="container mx-auto px-4 py-8 mb-12">
                {/* Breadcrumb */}
                <nav
                    className="text-sm text-gray-500 mb-6"
                    aria-label="Breadcrumb"
                >
                    <ol className="inline-flex items-center space-x-2">
                        <li>
                            <Link href="/" className="hover:underline">
                                Beranda
                            </Link>
                        </li>
                        <li>›</li>
                        <li>
                            <Link href="/events" className="hover:underline">
                                Events
                            </Link>
                        </li>
                        <li>›</li>
                        <li className="text-gray-800 font-medium">
                            {event.title}
                        </li>
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
                                    {new Date(
                                        event.start_date
                                    ).toLocaleDateString("id-ID", {
                                        day: "numeric",
                                        month: "long",
                                        year: "numeric",
                                    })}
                                </span>
                                <span className="flex items-center">
                                    <Clock className="w-4 h-4 mr-1" />
                                    {new Date(
                                        event.start_date
                                    ).toLocaleTimeString("id-ID", {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    })}{" "}
                                    -{" "}
                                    {new Date(
                                        event.end_date
                                    ).toLocaleTimeString("id-ID", {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    })}
                                </span>
                            </div>
                            <div className="mt-2 text-gray-600">
                                <span className="flex items-center">
                                    <MapPin className="w-4 h-4 mr-1" />{" "}
                                    {event.location}
                                </span>
                            </div>

                            <div className="flex flex-wrap gap-2 mt-2">
                                {event.categories.map((cat) => (
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
                            dangerouslySetInnerHTML={{
                                __html: event.description,
                            }}
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
                                <p className="text-sm text-gray-500">
                                    Diselenggarakan oleh
                                </p>
                                <p className="font-medium text-gray-800">
                                    {event.user.name}
                                </p>
                            </div>
                        </div>

                        {/* Ticket Types */}
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <h2 className="text-lg font-semibold text-gray-800 mb-4">
                                Pilih Tiket
                            </h2>
                            {/* Baris Kuota & Harga */}
                            <div className="flex items-baseline justify-between">
                                <div>
                                    <span className="text-lg font-medium text-gray-800">
                                        {event.quota} kursi
                                    </span>
                                    <p className="text-sm text-gray-500">
                                        tersisa
                                    </p>
                                </div>
                                <span className="text-lg font-semibold text-gray-800">
                                    {priceLabel}
                                </span>
                            </div>
                            {/* Baris Quantity & Pesan */}
                            <div className="flex items-center space-x-2 mt-4 justify-end">
                                {auth.user ? (
                                    <Link
                                        as="button"
                                        href={route('order-event.order', { id: event.id })}
                                        className="ml-2 px-4 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition"
                                    >
                                        Pesan
                                    </Link>
                                ) : (
                                    <Link
                                        as="button"
                                        href={route('login', { redirect: window.location.pathname + window.location.search })}
                                        className="ml-2 px-4 py-1 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition"
                                    >
                                        Login untuk Pesan Tiket
                                    </Link>
                                )}
                            </div>
                        </div>

                        {/* Share Section */}
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <p className="text-sm text-gray-600 mb-2">
                                Bagikan:
                            </p>
                            <div className="flex space-x-3">
                                <a
                                    href="#"
                                    className="text-blue-600 hover:text-blue-800"
                                >
                                    Facebook
                                </a>
                                <a
                                    href="#"
                                    className="text-blue-400 hover:text-blue-600"
                                >
                                    Twitter
                                </a>
                                <a
                                    href="#"
                                    className="text-pink-500 hover:text-pink-700"
                                >
                                    Instagram
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
            <Footer />
        </>
    );
}
