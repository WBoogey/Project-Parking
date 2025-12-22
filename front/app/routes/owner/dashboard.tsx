import { Link, useNavigate } from "react-router";
import { toast } from "sonner";
import Button from "@/components/atoms/Button";
import { SkeletonCard } from "@/components/atoms/Skeleton";
import EmptyState from "@/components/atoms/EmptyState";
import { useDeleteParking, useOwnerParkings } from "@/hooks/useOwner";

export default function OwnerDashboard() {
  const navigate = useNavigate();
  const { data: parkings, isLoading, error } = useOwnerParkings();
  const deleteMutation = useDeleteParking();

  const totalCapacity = parkings?.reduce((acc, p) => acc + p.capacity, 0) || 0;
  const totalParkings = parkings?.length || 0;

  if (isLoading) {
    return (
      <div className="flex flex-col gap-8">
        <div className="flex items-center justify-between">
          <div className="h-8 w-48 bg-gray-200 rounded-xl animate-pulse" />
          <div className="h-10 w-40 bg-gray-200 rounded-xl animate-pulse" />
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <SkeletonCard />
          <SkeletonCard />
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="p-8 text-center">
        <p className="text-red-500 mb-4">
          Erreur lors du chargement des parkings.
        </p>
      </div>
    );
  }

  return (
    <div className="flex flex-col gap-8">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-secondary">Tableau de bord</h1>
        <Link to="/owner/parkings/add">
          <Button onClick={() => {}}>Ajouter un parking</Button>
        </Link>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          className="bg-white p-6 rounded-3xl border border-tertiary/20 animate-fade-in-up"
          style={{ opacity: 0 }}
        >
          <h3 className="text-lg font-medium text-tertiary mb-2">
            Parkings gérés
          </h3>
          <p className="text-4xl font-bold text-secondary">{totalParkings}</p>
        </div>
        <div
          className="bg-white p-6 rounded-3xl border border-tertiary/20 animate-fade-in-up animation-delay-100"
          style={{ opacity: 0 }}
        >
          <h3 className="text-lg font-medium text-tertiary mb-2">
            Capacité totale
          </h3>
          <p className="text-4xl font-bold text-secondary">{totalCapacity}</p>
        </div>
      </div>

      <h2 className="text-xl font-bold text-secondary mt-4">Vos parkings</h2>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {parkings?.map((parking, index) => (
          <div
            key={parking.id}
            className={`bg-white p-6 rounded-3xl border border-tertiary/20 flex flex-col gap-4 animate-fade-in-up ${
              index === 0
                ? "animation-delay-200"
                : index === 1
                  ? "animation-delay-300"
                  : "animation-delay-400"
            }`}
            style={{ opacity: 0 }}
          >
            <div>
              <h3 className="text-lg font-semibold text-secondary">
                {parking.location}
              </h3>
              <p className="text-tertiary">{parking.capacity} places</p>
            </div>
            <div className="flex flex-col gap-2">
              <div className="flex gap-2">
                <Button
                  onClick={() => navigate(`/owner/parkings/${parking.id}/edit`)}
                  size="sm"
                  variant="outline"
                  className="flex-1"
                >
                  Modifier
                </Button>
                <Button
                  onClick={() =>
                    navigate(`/owner/parkings/${parking.id}/rates`)
                  }
                  size="sm"
                  className="flex-1"
                >
                  Tarifs
                </Button>
              </div>
              <Button
                onClick={() => {
                  deleteMutation.mutate(parking.id, {
                    onSuccess: () => {
                      toast.success("Parking supprimé");
                    },
                    onError: () => {
                      toast.error("Erreur lors de la suppression");
                    },
                  });
                }}
                size="sm"
                variant="outline"
                className="text-red-500 border-red-500 hover:bg-red-50"
                disabled={deleteMutation.isPending}
              >
                Supprimer
              </Button>
            </div>
          </div>
        ))}

        {parkings?.length === 0 && (
          <div className="col-span-full bg-white rounded-3xl border border-dashed border-gray-300">
            <EmptyState
              icon="🏢"
              title="Aucun parking"
              description="Vous n'avez pas encore ajouté de parking."
              action={
                <Link to="/owner/parkings/add">
                  <Button onClick={() => {}} variant="outline">
                    Ajouter mon premier parking
                  </Button>
                </Link>
              }
            />
          </div>
        )}
      </div>
    </div>
  );
}
