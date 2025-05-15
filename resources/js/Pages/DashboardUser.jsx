import Footer from '@/components/Footer';
import HeroSection from '@/components/HeroSection';
import Navbar from '@/components/Navbar';
import EventPage from '@/components/EventPage';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ dataevent, category }) {
    return (
        <
        >
            <Head title="Dashboard" />

            <div className="min-h-screen bg-gray-50">
                <Navbar />
                <HeroSection />
                {/* <Timer /> */}
                <EventPage dataevent={dataevent} categories={category} />
                <Footer />
            </div>
        </>
    );
}
