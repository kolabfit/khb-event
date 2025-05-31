import Footer from '@/components/Footer';
import HeroSection from '@/components/HeroSection';
import EventPage from '@/components/EventPage';
import GuestLayout from '@/Layouts/GuestLayout';
import Navbar from '@/components/Navbar';
import { Head } from '@inertiajs/react';

export default function DashboardUser({ auth, dataevent, category }) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="min-h-screen bg-gray-50">
                <Navbar auth={auth} />
                <HeroSection />
                <EventPage 
                    dataevent={dataevent} 
                    categories={category} 
                    events={dataevent?.data || []} 
                />
                <Footer />
            </div>
        </>
    );
}
