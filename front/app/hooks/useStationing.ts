import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { stationingApi } from "@/api/stationingApi";
import { queryKeys } from "@/api/queryKeyFactory";

export const useStationings = () => {
  return useQuery({
    queryKey: queryKeys.stationing.list(),
    queryFn: stationingApi.getStationings,
  });
};

export const useActiveStationing = (parkingId: string) => {
  return useQuery({
    queryKey: queryKeys.stationing.active(parkingId),
    queryFn: () => stationingApi.getActiveStationing(parkingId),
    enabled: !!parkingId,
  });
};

export const useEnterParking = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (parkingId: string) => stationingApi.enterParking(parkingId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.stationing.list() });
      queryClient.invalidateQueries({
        queryKey: queryKeys.customer.stationings(),
      });
    },
  });
};

export const useExitParking = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (parkingId: string) => stationingApi.exitParking(parkingId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.stationing.list() });
      queryClient.invalidateQueries({
        queryKey: queryKeys.customer.stationings(),
      });
    },
  });
};

