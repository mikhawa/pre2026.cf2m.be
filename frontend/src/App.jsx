import { Route, Routes } from 'react-router';
import Accueil from './pages/Accueil';
import PageIntrouvable from './pages/PageIntrouvable';

export default function App() {
    return (
        <Routes>
            <Route path="/" element={<Accueil />} />
            <Route path="*" element={<PageIntrouvable />} />
        </Routes>
    );
}
