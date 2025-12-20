import { useQuery } from "@tanstack/react-query";
import { parkingApi } from "@/api/parkingApi";

export const useParkings = (search?: string) => {
  return useQuery({
    queryKey: ["parkings", "list", search],
    queryFn: () => parkingApi.getParkings(search),
  });
};

export const useParking = (id?: string) => {
  return useQuery({
    queryKey: ["parkings", "detail", id],
    queryFn: () => (id ? parkingApi.getParkingById(id) : undefined),
    enabled: !!id,
  });
};
