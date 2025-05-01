import React, { useState } from 'react';

const Footer = () => {
  const [email, setEmail] = useState('');
  
  const handleSubscribe = () => {
    // Handle subscription logic here
    console.log('Subscribing email:', email);
    setEmail('');
    alert('Terima kasih telah berlangganan!');
  };

  return (
    <div className="container mx-auto py-10">
      <div className="flex flex-wrap items-center justify-between">
        {/* Stats Section */}
        <div className="flex space-x-12">
          <div className="text-center">
            <h2 className="text-4xl font-bold">1.542</h2>
            <p className="text-gray-600">Total Event Aktif</p>
          </div>
          
          <div className="text-center">
            <h2 className="text-4xl font-bold">25.390</h2>
            <p className="text-gray-600">Tiket Terjual</p>
          </div>
          
          <div className="text-center">
            <h2 className="text-4xl font-bold">320+</h2>
            <p className="text-gray-600">EO Terdaftar</p>
          </div>
        </div>
        
        {/* Subscribe Section */}
        <div className="w-80">
          <h3 className="font-medium mb-3">
            Dapatkan info event menarik langsung isi emailmu!
          </h3>
          
          <div>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="Masukkan email kamu"
              className="w-full p-2 border border-gray-300 rounded-md mb-2"
            />
            
            <button
              onClick={handleSubscribe}
              className="w-full bg-green-300 hover:bg-green-400 py-2 px-4 rounded-md font-medium transition-colors"
            >
              Berlangganan
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Footer;