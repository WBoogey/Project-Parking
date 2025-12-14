import { useNavigate } from "react-router";
import { useMutation } from "@tanstack/react-query";
import AuthForm from "@/components/organisms/AuthForm";
import apiClient from "@/api/client";
import type { AuthFormData } from "@/types/AuthFormTypes";

export default function RegisterPro() {
  const navigate = useNavigate();

  const mutation = useMutation({
    mutationFn: async (data: AuthFormData) => {
      const response = await apiClient.post("/auth/signup", {
        firstName: data.firstName,
        lastName: data.lastName,
        email: data.email,
        password: data.password,
        role: "owner",
        companyName: data.companyName,
        address: data.address,
        city: data.city,
      });
      return response.data;
    },
    onSuccess: () => {
      navigate("/owner/dashboard");
    },
    onError: (error) => {
      console.error("Registration failed:", error);
    },
  });

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 p-4">
      <AuthForm
        mode="signup-pro"
        onSubmit={(data) => mutation.mutate(data)}
        onNavigateToLogin={() => navigate("/login")}
        onModeChange={(mode) => {
          if (mode === "signup") navigate("/register");
        }}
        onBackToSite={() => navigate("/")}
      />
    </div>
  );
}
