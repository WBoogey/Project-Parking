import type {
  SubscriptionDetail,
  SubscriptionType,
} from "@/types/SubscriptionTypes";
import Button from "@/components/atoms/Button";
import { cn } from "cn-utility";
import { useState } from "react";

const TYPE_LABELS: Record<SubscriptionType, string> = {
  total: "Total (24/7)",
  weekend: "Week-end",
  evening: "Soir",
  custom: "Personnalisé",
};

const STATUS_STYLES = {
  active: "bg-available/20 text-green-700",
  cancelled: "bg-gray-100 text-gray-700",
  expired: "bg-no-availability/20 text-red-700",
};

const STATUS_LABELS = {
  active: "Actif",
  cancelled: "Annulé",
  expired: "Expiré",
};

interface SubscriptionCardProps {
  subscription: SubscriptionDetail;
  onViewDetails?: (id: string) => void;
  onCancel?: (id: string) => void;
  isLoading?: boolean;
  className?: string;
}

const SubscriptionCard = ({
  subscription,
  onViewDetails,
  onCancel,
  isLoading = false,
  className,
}: SubscriptionCardProps) => {
  const [showConfirm, setShowConfirm] = useState(false);

  const handleCancelClick = () => {
    setShowConfirm(true);
  };

  const handleConfirmCancel = () => {
    onCancel?.(subscription.id);
    setShowConfirm(false);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("fr-FR", {
      day: "numeric",
      month: "long",
      year: "numeric",
    });
  };

  return (
    <div
      className={cn(
        "bg-white p-6 rounded-2xl border border-tertiary/20",
        className,
      )}
    >
      <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div className="flex-1">
          <div className="flex items-center gap-3 mb-2">
            <p className="font-bold text-secondary">
              {subscription.parkingName ??
                `Parking #${subscription.parkingId.substring(0, 8)}...`}
            </p>
            <span
              className={cn(
                "px-3 py-1 rounded-xl text-xs font-medium",
                STATUS_STYLES[subscription.status],
              )}
            >
              {STATUS_LABELS[subscription.status]}
            </span>
          </div>

          <p className="text-accent font-medium">
            {TYPE_LABELS[subscription.subscriptionType]}
          </p>

          <p className="text-tertiary text-sm mt-2">
            Du {formatDate(subscription.startDate)} au{" "}
            {formatDate(subscription.endDate)}
          </p>

          {subscription.monthlyPrice > 0 && (
            <p className="text-secondary font-semibold mt-2">
              {subscription.monthlyPrice}€ / mois
            </p>
          )}
        </div>

        <div className="flex flex-col sm:flex-row gap-2">
          {onViewDetails && (
            <Button
              size="sm"
              variant="outline"
              onClick={() => onViewDetails(subscription.id)}
              disabled={isLoading}
            >
              Détails
            </Button>
          )}

          {onCancel && subscription.status === "active" && !showConfirm && (
            <Button
              size="sm"
              variant="plain"
              onClick={handleCancelClick}
              disabled={isLoading}
              className="text-no-availability hover:text-no-availability"
            >
              Annuler
            </Button>
          )}

          {showConfirm && (
            <div className="flex items-center gap-2">
              <Button
                size="sm"
                variant="default"
                onClick={handleConfirmCancel}
                disabled={isLoading}
                className="bg-no-availability hover:bg-no-availability/80"
              >
                Confirmer
              </Button>
              <Button
                size="sm"
                variant="outline"
                onClick={() => setShowConfirm(false)}
                disabled={isLoading}
              >
                Non
              </Button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default SubscriptionCard;
