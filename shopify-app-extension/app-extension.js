// Shopify App Extension for Identify Provider
// This file is used by the Shopify App Bridge to inject our identify provider functionality

import { register } from '@shopify/web-pixels-extension';

register(({ configuration, browser, analytics }) => {
  // Load our identify provider script
  const script = document.createElement('script');
  script.src = 'https://your-app-domain.com/js/shopify-identify-provider.js';
  script.async = true;
  document.head.appendChild(script);
  
  // Log that the extension has been loaded
  console.log('Identify Provider extension loaded');
}); 