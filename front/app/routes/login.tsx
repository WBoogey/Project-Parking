import { useNavigate } from "react-router";
import { useMutation } from "@tanstack/react-query";
import AuthForm from "@/components/organisms/AuthForm";
import apiClient from "@/api/client";
import type { AuthFormData } from "@/types/AuthFormTypes";

export default function Login() {
  const navigate = useNavigate();

  const mutation = useMutation({
    mutationFn: async (data: AuthFormData) => {
      const response = await apiClient.post("/auth/signin", {
        email: data.email,
        password: data.password,
      });
      return response.data;
    },
    onSuccess: () => {
      navigate("/");
    },
    onError: (error) => {
      console.error("Login failed:", error);
    },
  });

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 p-4">
      <AuthForm
        mode="login"
        onSubmit={(data) => mutation.mutate(data)}
        onNavigateToSignup={() => navigate("/register")}
        onBackToSite={() => navigate("/")}
      />
    </div>
  );
}
