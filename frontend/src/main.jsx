import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router';
import App from './App';

// Préfixe des routes : '/spa' sur la préprod (public/spa/index.html, fourni par la CI), '/' en dev
const basename = import.meta.env.VITE_ROUTER_BASENAME ?? '/';

createRoot(document.getElementById('root')).render(
    <StrictMode>
        <BrowserRouter basename={basename}>
            <App />
        </BrowserRouter>
    </StrictMode>,
);
