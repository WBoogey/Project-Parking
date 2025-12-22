import { Link, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import { useDeleteParking, useOwnerParkings } from "@/hooks/useOwner";

export default function OwnerDashboard() {
  const navigate = useNavigate();
  const { data: parkings, isLoading, error } = useOwnerParkings();
  const deleteMutation = useDeleteParking();

  const totalCapacity = parkings?.reduce((acc, p) => acc + p.capacity, 0) || 0;
  const totalParkings = parkings?.length || 0;

  if (isLoading) return <div className="p-8 text-center">Chargement...</div>;

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
        <div className="bg-white p-6 rounded-3xl border border-tertiary/20">
          <h3 className="text-lg font-medium text-tertiary mb-2">
            Parkings gérés
          </h3>
          <p className="text-4xl font-bold text-secondary">{totalParkings}</p>
        </div>
        <div className="bg-white p-6 rounded-3xl border border-tertiary/20">
          <h3 className="text-lg font-medium text-tertiary mb-2">
            Capacité totale
          </h3>
          <p className="text-4xl font-bold text-secondary">{totalCapacity}</p>
        </div>
      </div>

      <h2 className="text-xl font-bold text-secondary mt-4">Vos parkings</h2>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {parkings?.map((parking) => (
          <div
            key={parking.id}
            className="bg-white p-6 rounded-3xl border border-tertiary/20 flex flex-col gap-4"
          >
            <div>
              <h3 className="text-lg font-semibold text-secondary">
                {parking.location}
              </h3>
              <p className="text-tertiary">
                {parking.capacity} places
              </p>
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
                  onClick={() => navigate(`/owner/parkings/${parking.id}/rates`)}
                  size="sm"
                  className="flex-1"
                >
                  Tarifs
                </Button>
              </div>
              <Button
                onClick={() => {
                  if (confirm("Voulez-vous vraiment supprimer ce parking ?")) {
                    deleteMutation.mutate(parking.id);
                  }
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
          <div className="col-span-full text-center py-12 bg-white rounded-3xl border border-dashed border-gray-300">
            <p className="text-gray-500 mb-4">
              Vous n&apos;avez pas encore ajouté de parking.
            </p>
            <Link to="/owner/parkings/add" className="inline-block">
              <Button
                onClick={() => {}}
                variant="outline"
                className="w-auto px-8"
              >
                Ajouter mon premier parking
              </Button>
            </Link>
          </div>
        )}
      </div>
    </div>
  );
}
