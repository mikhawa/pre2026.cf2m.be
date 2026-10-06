import { jsxs as _jsxs } from "react/jsx-runtime";
/**
 * Composant réutilisable d'exemple : un bouton compteur.
 * Les composants de ce dossier ne sont pas appelés depuis Twig,
 * ils sont importés par les îlots (dossier islands/).
 */
export default function Counter({
  value,
  onIncrement
}) {
  return /*#__PURE__*/_jsxs("button", {
    type: "button",
    className: "btn btn-primary btn-sm",
    onClick: onIncrement,
    children: ["Cliqu\xE9 ", value, " fois"]
  });
}