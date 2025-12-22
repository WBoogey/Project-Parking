import { useMutation, useQueryClient } from "@tanstack/react-query";
import { stationingApi } from "@/api/stationingApi";
import { queryKeys } from "@/api/queryKeyFactory";

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
