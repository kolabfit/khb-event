// resources/js/App.jsx
import React from 'react';
import Navbar from './components/Navbar';
import HeroSection from './components/HeroSection';
import Timer from './components/Timer';
import EventPopuler from './components/EventPopuler';
import EventCards from './components/EventCard';
import EventPage from './components/EventPage';
import EventTips from './components/EventTips';
import Footer from './components/Footer';

export default function App() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <HeroSection />
      <Timer />
      <EventPage />
    </div>
  );
}