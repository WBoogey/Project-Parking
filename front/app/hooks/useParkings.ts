import { useQuery } from "@tanstack/react-query";
import { parkingApi } from "@/api/parkingApi";
import { queryKeys } from "@/api/queryKeyFactory";

export const useParkings = (search?: string) => {
  return useQuery({
    queryKey: queryKeys.parking.list(search),
    queryFn: () => parkingApi.getParkings(),
    select: (data) => {
      if (!search) return data;
      const lowerSearch = search.toLowerCase();
      return data.filter((p) => p.location.toLowerCase().includes(lowerSearch));
    },
  });
};

export const useParking = (id?: string) => {
  return useQuery({
    queryKey: queryKeys.parking.detail(id || ""),
    queryFn: () => (id ? parkingApi.getParkingById(id) : undefined),
    enabled: !!id,
  });
};
