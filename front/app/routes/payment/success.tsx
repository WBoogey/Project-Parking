import { useNavigate, useSearchParams } from "react-router";
import Button from "@/components/atoms/Button";

export default function PaymentSuccess() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const sessionId = searchParams.get("session_id");

  return (
    <div className="min-h-[60vh] flex items-center justify-center">
      <div className="bg-white p-12 rounded-3xl max-w-lg mx-auto shadow-sm border border-gray-100 text-center">
        <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg
            className="w-10 h-10 text-green-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M5 13l4 4L19 7"
            />
          </svg>
        </div>

        <h1 className="text-2xl font-bold text-secondary mb-4">
          Paiement réussi !
        </h1>

        <p className="text-tertiary mb-8">
          Votre abonnement a été activé avec succès. Vous pouvez maintenant
          profiter de votre place de parking.
        </p>

        {sessionId && (
          <p className="text-xs text-tertiary/60 mb-6">
            Référence : {sessionId.slice(0, 20)}...
          </p>
        )}

        <div className="flex flex-col gap-3">
          <Button size="full" onClick={() => navigate("/customer")}>
            Voir mes abonnements
          </Button>
          <button
            type="button"
            onClick={() => navigate("/")}
            className="text-tertiary hover:text-secondary transition-colors cursor-pointer"
          >
            Retour à l&apos;accueil
          </button>
        </div>
      </div>
    </div>
  );
}
