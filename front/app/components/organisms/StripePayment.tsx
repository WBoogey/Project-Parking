interface StripePaymentProps {
  isLoading: boolean;
  error?: Error | null;
  onRetry?: () => void;
}

export default function StripePayment({
  isLoading,
  error,
  onRetry,
}: StripePaymentProps) {
  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-2xl p-6 text-center">
        <div className="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg
            className="w-6 h-6 text-red-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
            />
          </svg>
        </div>
        <h3 className="font-semibold text-red-800 mb-2">Erreur de paiement</h3>
        <p className="text-red-600 text-sm mb-4">
          Une erreur est survenue lors de la création de la session de paiement.
        </p>
        {onRetry && (
          <button
            type="button"
            onClick={onRetry}
            className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors cursor-pointer"
          >
            Réessayer
          </button>
        )}
      </div>
    );
  }

  if (isLoading) {
    return (
      <div className="bg-blue-50 border border-blue-200 rounded-2xl p-8 text-center">
        <div className="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-4" />
        <h3 className="font-semibold text-blue-800 mb-2">
          Redirection vers Stripe...
        </h3>
        <p className="text-blue-600 text-sm">
          Veuillez patienter, vous allez être redirigé vers la page de paiement
          sécurisée.
        </p>
      </div>
    );
  }

  return null;
}
