import { useState } from 'react';
import Counter from '../../components/Counter.js';

/**
 * Îlot de démonstration : vérifie que la chaîne Twig → Stimulus → React
 * fonctionne (props, état, rendu, navigation Turbo).
 * Affiché uniquement en environnement dev sur la page d'accueil.
 */
import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
export default function HelloIsland({
  name
}) {
  const [count, setCount] = useState(0);
  return /*#__PURE__*/_jsxs("div", {
    className: "alert alert-info my-4",
    role: "status",
    children: [/*#__PURE__*/_jsxs("p", {
      className: "mb-2",
      children: ["\xCElot React actif \u2014 bonjour ", /*#__PURE__*/_jsx("strong", {
        children: name
      }), " !"]
    }), /*#__PURE__*/_jsx(Counter, {
      value: count,
      onIncrement: () => setCount(count + 1)
    })]
  });
}