package com.student.student.repository;

import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;
import org.springframework.stereotype.Repository;

import com.student.student.entity.Customer;
import java.util.List;

@Repository
public interface CustomerRepository extends CrudRepository<Customer, Integer> {


    @Query("SELECT c FROM Customer c WHERE c.name LIKE %?1%")
    List<Customer> findByName(String name);
}
