import { useNavigate, useSearchParams } from "react-router";
import Button from "@/components/atoms/Button";

export default function PaymentCancel() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const subscriptionId = searchParams.get("subscription_id");

  const handleRetry = () => {
    if (subscriptionId) {
      navigate(-1);
    } else {
      navigate("/search");
    }
  };

  return (
    <div className="min-h-[60vh] flex items-center justify-center">
      <div className="bg-white p-12 rounded-3xl max-w-lg mx-auto shadow-sm border border-gray-100 text-center">
        <div className="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg
            className="w-10 h-10 text-orange-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </div>

        <h1 className="text-2xl font-bold text-secondary mb-4">
          Paiement annulé
        </h1>

        <p className="text-tertiary mb-8">
          Votre paiement a été annulé. Aucun montant n&apos;a été débité de
          votre compte. Vous pouvez réessayer à tout moment.
        </p>

        <div className="flex flex-col gap-3">
          <Button size="full" onClick={handleRetry}>
            Réessayer
          </Button>
          <button
            type="button"
            onClick={() => navigate("/search")}
            className="text-tertiary hover:text-secondary transition-colors cursor-pointer"
          >
            Rechercher un autre parking
          </button>
        </div>
      </div>
    </div>
  );
}
