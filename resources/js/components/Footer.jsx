import React from 'react';
import { Mail, Phone, MapPin, Facebook, Twitter, Instagram } from 'lucide-react';

const Footer = () => {
  return (
    <footer className="bg-gray-900 text-gray-300 py-12">
      <div className="container mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-16">
        {/* Column 1: Branding */}
        <div>
          <h3 className="text-2xl font-bold text-white mb-4">KHB Event</h3>
          <p className="text-sm leading-relaxed">
            Platform terbaik untuk menemukan dan mengelola event  
            favoritmu. Dari konser hingga workshop—semua ada di  
            satu tempat!
          </p>
        </div>

        {/* Column 2: Contact — teks & kontainer rata kanan */}
        <div className="text-right md:justify-self-end">
          <h4 className="font-semibold mb-3">Contact Us</h4>
          <ul className="space-y-3 text-sm">
            <li className="flex items-center justify-end">
              <MapPin className="w-4 h-4 ml-2 text-purple-500" />
               Jl. Merdeka No.10, Jakarta
            </li>
            <li className="flex items-center justify-end">
              <Mail className="w-4 h-4 ml-2 text-purple-500" />
                support@khbevent.com
            </li>
            <li className="flex items-center justify-end">
              <Phone className="w-4 h-4 ml-2 text-purple-500" />
               +62 812 3456 7890
            </li>
          </ul>
          <div className="mt-4 flex justify-end space-x-3">
            <a href="#" className="hover:text-white">
              <Facebook className="w-5 h-5" />
            </a>
            <a href="#" className="hover:text-white">
              <Twitter className="w-5 h-5" />
            </a>
            <a href="#" className="hover:text-white">
              <Instagram className="w-5 h-5" />
            </a>
          </div>
        </div>
      </div>

      <div className="border-t border-gray-700 mt-8 pt-4 text-center text-xs">
        © {new Date().getFullYear()} KHB Event. All rights reserved.
      </div>
    </footer>
  );
};

export default Footer;
