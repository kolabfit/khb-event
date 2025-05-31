import Footer from '@/Components/Footer';
import HeroSection from '@/Components/HeroSection';
import Navbar from '@/Components/Navbar';
import EventPage from '@/Components/EventPage';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth, dataevent, category, events }) {
    return (
        <
        >
            <Head title="Dashboard" />
            <div className="min-h-screen bg-gray-50">
                <Navbar auth={auth}/>
                <HeroSection />
                {/* <Timer /> */}
                <EventPage dataevent={dataevent} categories={category} catevents={events} />
                <Footer />
            </div>
        </>
    );
}