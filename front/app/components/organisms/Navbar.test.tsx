import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import Navbar from "./Navbar";
import { BrowserRouter } from "react-router";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

const createTestQueryClient = () =>
  new QueryClient({
    defaultOptions: {
      queries: {
        retry: false,
      },
    },
  });

const renderWithProviders = (ui: React.ReactElement) => {
  const testQueryClient = createTestQueryClient();
  return render(
    <QueryClientProvider client={testQueryClient}>
      <BrowserRouter>{ui}</BrowserRouter>
    </QueryClientProvider>,
  );
};

describe("Navbar", () => {
  it("should render navbar", () => {
    renderWithProviders(<Navbar />);

    expect(screen.getByRole("navigation")).toBeInTheDocument();
  });

  it("should display ParkShare brand", () => {
    renderWithProviders(<Navbar />);

    expect(screen.getByText("ParkShare")).toBeInTheDocument();
  });

  it("should display login and register buttons when not authenticated", async () => {
    renderWithProviders(<Navbar />);

    await waitFor(() => {
      expect(screen.getByText("Se connecter")).toBeInTheDocument();
    });
    expect(screen.getByText("S'inscrire")).toBeInTheDocument();
  });
});
