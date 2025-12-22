import Button from "@/components/atoms/Button";
import { Link } from "react-router";

export function meta() {
  return [
    { title: "Project Parking" },
    { name: "description", content: "Trouvez votre place de parking idéale" },
  ];
}

export default function Home() {
  return (
    <div className="flex flex-col items-center justify-center px-4 py-20 text-center">
      <h1 className="text-5xl md:text-6xl font-bold text-secondary mb-6 max-w-4xl">
        La solution simplifiée pour votre stationnement
      </h1>
      <p className="text-xl text-tertiary mb-12 max-w-2xl">
        Trouvez une place en quelques clics ou rentabilisez votre parking
        inutilisé. Simple, sécurisé et rapide.
      </p>

      <div className="flex flex-col sm:flex-row gap-4 w-full max-w-md justify-center">
        <Link to="/search" className="flex-1">
          <Button size="full" className="h-14 text-lg">
            Trouver une place
          </Button>
        </Link>
        <Link to="/register/pro" className="flex-1">
          <Button variant="outline" size="full" className="h-14 text-lg">
            Devenir propriétaire
          </Button>
        </Link>
      </div>

      <div className="mt-24 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl w-full text-left">
        <div className="p-8 rounded-3xl bg-white border border-tertiary/20">
          <div className="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mb-6">
            <span className="text-2xl">📍</span>
          </div>
          <h3 className="text-xl font-bold text-secondary mb-3">
            Localisation idéale
          </h3>
          <p className="text-tertiary">
            Des parkings situés au cœur des villes, proches de vos points
            d&apos;intérêt.
          </p>
        </div>
        <div className="p-8 rounded-3xl bg-white border border-tertiary/20">
          <div className="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mb-6">
            <span className="text-2xl">🔒</span>
          </div>
          <h3 className="text-xl font-bold text-secondary mb-3">
            Sécurité garantie
          </h3>
          <p className="text-tertiary">
            Tous nos parkings sont vérifiés et sécurisés pour votre
            tranquillité.
          </p>
        </div>
        <div className="p-8 rounded-3xl bg-white border border-tertiary/20">
          <div className="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mb-6">
            <span className="text-2xl">💳</span>
          </div>
          <h3 className="text-xl font-bold text-secondary mb-3">
            Paiement simple
          </h3>
          <p className="text-tertiary">
            Réservez et payez en ligne instantanément, sans frais cachés.
          </p>
        </div>
      </div>
    </div>
  );
}
