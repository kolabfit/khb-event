import React from 'react';
import { Mail, Phone, MapPin, Facebook, Twitter, Instagram } from 'lucide-react';

const Footer = () => {
  return (
    <footer className="bg-gray-900 text-gray-400 py-10 md:py-12">
      <div className="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 items-start">
        {/* Column 1: Branding */}
        <div className="text-left mb-8 md:mb-0">
          <h3 className="text-2xl font-bold text-white mb-3">KHB Event</h3>
          <p className="text-sm leading-relaxed max-w-sm">
            Platform terbaik untuk menemukan dan mengelola event  
            favoritmu. Dari konser hingga workshop—semua ada di  
            satu tempat!
          </p>
        </div>

        {/* Column 2: Hubungi Kami */}
        <div className="space-y-6 text-left mb-8 md:mb-0">
          <div>
            <h4 className="font-semibold text-white mb-3">Hubungi Kami</h4>
            <ul className="space-y-2 text-sm text-gray-400">
              {/* Hotline KHB */}
              <li className="flex items-center">
                <Phone className="w-4 h-4 mr-2 text-purple-400 flex-shrink-0" />
                Tel: +62 8562394568
              </li>
              {/* Email */}
              <li className="flex items-center">
                <Mail className="w-4 h-4 mr-2 text-purple-400 flex-shrink-0" />
                Email: komunitas halalbandung@gmail.com
              </li>
            </ul>
          </div>
        </div>

        {/* Column 3: Contact Info & Social */}
        <div className="space-y-6 text-left md:text-right">
          {/* Contact Info */}
          <div>
            <h4 className="font-semibold text-white mb-3">Contact Info</h4>
            <ul className="space-y-2 text-sm text-gray-400">
              <li className="flex items-start md:justify-end text-left md:text-right">
                <MapPin className="w-4 h-4 mr-2 md:ml-2 md:mr-0 text-purple-400 flex-shrink-0" />
                <span>
                  Griya Permata Asri A3 No 5, RT 01/13,<br/>
                  Lengkong, Kec. Bojongsoang,<br/>
                  Kabupaten Bandung, Jawa Barat 40287
                </span>
              </li>
              {/* Operating Hours */}
              <li className="flex items-center md:justify-end">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-4 h-4 mr-2 md:ml-2 md:mr-0 text-purple-400 flex-shrink-0">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Senin - Jumat<br/>08.00 - 17.00</span>
              </li>
               <li className="text-sm text-gray-500 mt-1 text-left md:text-right">
                  Untuk konsultasi offline by appointment
               </li>
            </ul>
          </div>

          {/* Social Media Icons
          <div className="flex space-x-3 mt-4 justify-start md:justify-end">
            <a href="#" className="text-gray-400 hover:text-white">
              <Facebook className="w-5 h-5" />
            </a>
            <a href="#" className="text-gray-400 hover:text-white">
              <Twitter className="w-5 h-5" />
            </a>
            <a href="#" className="text-gray-400 hover:text-white">
              <Instagram className="w-5 h-5" />
            </a>
          </div> */}
        </div>
      </div>

      <div className="border-t border-gray-700 mt-8 pt-4 text-center text-xs">
        © {new Date().getFullYear()} KHB Event. All rights reserved.
      </div>
    </footer>
  );
};

export default Footer;
